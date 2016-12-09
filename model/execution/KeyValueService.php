<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\model\execution;

use common_Logger;
use common_persistence_KeyValuePersistence;
use core_kernel_classes_Resource;
use oat\oatbox\service\ConfigurableService;
use taoDelivery_models_classes_execution_Service;
use oat\taoDelivery\models\classes\execution\DeliveryExecution as DeliveryExecutionWrapper;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @package taoDelivery
 */
class KeyValueService extends ConfigurableService implements taoDelivery_models_classes_execution_Service
{

    const OPTION_PERSISTENCE = 'persistence';

    const DELIVERY_EXECUTION_PREFIX = 'kve_de_';

    const USER_EXECUTIONS_PREFIX = 'kve_ue_';

    const USER_DELIVERY_PREFIX = 'kve_ud_';

    /**
     * @var common_persistence_KeyValuePersistence
     */
    private $persistence;

    /**
     * @return common_persistence_KeyValuePersistence|\common_persistence_Persistence
     */
    protected function getPersistence()
    {
        if (is_null($this->persistence)) {
            $persistenceOption = $this->getOption(self::OPTION_PERSISTENCE);
            $this->persistence = (is_object($persistenceOption))
                ? $persistenceOption
                : common_persistence_KeyValuePersistence::getPersistence($persistenceOption);
        }
        return $this->persistence;
    }

    /**
     * @param core_kernel_classes_Resource $compiled
     * @param string $userUri
     * @return array
     */
    public function getUserExecutions(core_kernel_classes_Resource $compiled, $userUri)
    {
        $returnValue = array();
        $data = $this->getPersistence()->get(self::USER_DELIVERY_PREFIX . $userUri . $compiled->getUri());
        $keys = $data !== false ? json_decode($data) : array();
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $returnValue[$key] = $this->getDeliveryExecution($key);
            }
        } else {
            common_Logger::w('Non array "' . gettype($keys) . '" received as active Execution Keys for user ' . $userUri . ' with delivery' . $compiled->getUri());
        }

        return $returnValue;
    }

    /**
     * Generate a new delivery execution
     *
     * @param core_kernel_classes_Resource $assembly
     * @param string $userId
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $userId)
    {
        $deImplementation = \taoDelivery_models_classes_execution_KVDeliveryExecution::spawn($this->getPersistence(),
            $userId, $assembly);
        $deliveryExecution = new DeliveryExecutionWrapper($deImplementation);

        $this->updateDeliveryExecutionStatus($deliveryExecution, null, DeliveryExecution::STATE_ACTIVE);

        $this->addDeliveryToUserExecutionList($userId, $assembly->getUri(), $deliveryExecution->getIdentifier());
        return $deliveryExecution;
    }

    /**
     * @param $userId
     * @param $assemblyId
     * @param $executionId
     */
    private function addDeliveryToUserExecutionList($userId, $assemblyId, $executionId)
    {
        $uid = self::USER_DELIVERY_PREFIX . $userId . $assemblyId;
        $data = json_decode($this->getPersistence()->get($uid));
        if (!$data) {
            $data = [];
        }
        $data [] = $executionId;
        $this->getPersistence()->set($uid, json_encode($data));
    }

    /**
     * @param string $userUri
     * @param string $status
     * @return array
     */
    public function getDeliveryExecutionsByStatus($userUri, $status)
    {
        $returnValue = array();
        $data = $this->getPersistence()->get(self::USER_EXECUTIONS_PREFIX . $userUri . $status);
        $keys = $data !== false ? json_decode($data) : array();
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $returnValue[$key] = $this->getDeliveryExecution($key);
            }
        } else {
            common_Logger::w('Non array "' . gettype($keys) . '" received as active Delivery Keys for user ' . $userUri);
        }

        return $returnValue;
    }

    /**
     * @param string $identifier
     * @return DeliveryExecution
     */
    public function getDeliveryExecution($identifier)
    {
        $deImplementation = new \taoDelivery_models_classes_execution_KVDeliveryExecution($this->getPersistence(),
            $identifier);
        return new DeliveryExecutionWrapper($deImplementation);
    }

    /**
     * Update the collection of deliveries
     *
     * @param DeliveryExecution $deliveryExecution
     * @param string $old
     * @param string $new
     */
    public function updateDeliveryExecutionStatus(DeliveryExecution $deliveryExecution, $old, $new)
    {

        $userId = $deliveryExecution->getUserIdentifier();
        if ($old != null) {
            $oldReferences = $this->getDeliveryExecutionsByStatus($userId, $old);
            foreach (array_keys($oldReferences) as $key) {
                if ($oldReferences[$key]->getIdentifier() == $deliveryExecution->getIdentifier()) {
                    unset($oldReferences[$key]);
                }
            }
            $this->setDeliveryExecutions($userId, $old, $oldReferences);
        }

        $newReferences = $this->getDeliveryExecutionsByStatus($userId, $new);
        $newReferences[] = $deliveryExecution;
        $this->setDeliveryExecutions($userId, $new, $newReferences);

    }

    /**
     * @param $deliveryExecutionId
     * @return mixed
     */
    public function getData($deliveryExecutionId)
    {
        $dataString = $this->getPersistence()->get($deliveryExecutionId);
        $data = json_decode($dataString, true);
        return $data;
    }

    /**
     * @param $userUri
     * @param $status
     * @param $executions
     * @return mixed
     */
    private function setDeliveryExecutions($userUri, $status, $executions)
    {
        $keys = array();
        foreach ($executions as $execution) {
            $keys[] = $execution->getIdentifier();
        }
        return $this->getPersistence()->set(self::USER_EXECUTIONS_PREFIX . $userUri . $status, json_encode($keys));
    }
}
