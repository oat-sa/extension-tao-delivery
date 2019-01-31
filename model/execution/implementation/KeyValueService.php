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
namespace oat\taoDelivery\model\execution\implementation;

use common_Logger;
use common_persistence_KeyValuePersistence;
use core_kernel_classes_Resource;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\execution\KVDeliveryExecution;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use oat\taoDelivery\model\execution\Service;
use oat\taoDelivery\model\execution\DeliveryExecution as DeliveryExecutionWrapper;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDeleteRequest;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @package taoDelivery
 */
class KeyValueService extends ConfigurableService implements Service
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
                : $this->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID)->getPersistenceById($persistenceOption);
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
     * Spawn a new Delivery Execution
     *
     * @param string $label
     * @param string $deliveryId
     * @param string $userId
     * @param string $status
     * @return \oat\taoDelivery\model\execution\DeliveryExecution
     */
    public function spawnDeliveryExecution($label, $deliveryId, $userId, $status)
    {
        $identifier = self::DELIVERY_EXECUTION_PREFIX . \common_Utils::getNewUri();
        $data = array(
            OntologyRdfs::RDFS_LABEL => $label,
            OntologyDeliveryExecution::PROPERTY_DELIVERY  => $deliveryId,
            OntologyDeliveryExecution::PROPERTY_SUBJECT => $userId,
            OntologyDeliveryExecution::PROPERTY_TIME_START => microtime(),
            OntologyDeliveryExecution::PROPERTY_STATUS => $status
        );
        $kvDe = new KVDeliveryExecution($this, $identifier, $data);
        $this->updateDeliveryExecutionStatus($kvDe, null, $status);
        $this->addDeliveryToUserExecutionList($userId, $deliveryId, $kvDe->getIdentifier());
        return $this->propagate(new DeliveryExecutionWrapper($kvDe));
    }

    /**
     * Generate a new delivery execution
     * @deprecated
     * @param core_kernel_classes_Resource $assembly
     * @param string $userId
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $userId)
    {
        common_Logger::w('Call to deprecated function '.__FUNCTION__);
        return $this->spawnDeliveryExecution($assembly->getLabel(), $assembly->getUri(), $userId,  KvDeliveryExecution::STATE_ACTIVE);
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
                $de = $this->getDeliveryExecution($key);
                if ($de->getState()->getUri() !== $status) {
                    $this->fixStatus($de->getImplementation(), $status, $de->getState()->getUri());
                } else {
                    $returnValue[$key] = $de;
                }
            }
        } else {
            common_Logger::w('Non array "' . gettype($keys) . '" received as active Delivery Keys for user ' . $userUri);
        }

        return $returnValue;
    }

    /**
     * @param string $identifier
     * @return DeliveryExecutionWrapper
     */
    public function getDeliveryExecution($identifier)
    {
        $identifier = ($identifier instanceof core_kernel_classes_Resource) ? $identifier->getUri() : (string) $identifier;

        $deImplementation = new KVDeliveryExecution($this, $identifier);

        return $this->propagate(new DeliveryExecutionWrapper($deImplementation));
    }

    /**
     * Update the collection of deliveries
     *
     * @param KVDeliveryExecution $deliveryExecution
     * @param string $old
     * @param string $new
     * @return mixed
     */
    public function updateDeliveryExecutionStatus(KVDeliveryExecution $deliveryExecution, $old, $new)
    {
        $this->update($deliveryExecution);
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
        $newReferences[$deliveryExecution->getIdentifier()] = $deliveryExecution;
        return $this->setDeliveryExecutions($userId, $new, $newReferences);
    }

    public function update(KVDeliveryExecution $de)
    {
        $this->getPersistence()->set($de->getIdentifier(), json_encode($de));
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
     * @inheritdoc
     */
    public function deleteDeliveryExecutionData(DeliveryExecutionDeleteRequest $request)
    {
        $deUri = $request->getDeliveryExecution()->getIdentifier();
        $deliveryUri = $request->getDeliveryResource()->getUri();
        $userUri = $request->getDeliveryExecution()->getUserIdentifier();

        /** @var \common_ext_ExtensionsManager $extManager */
        $extManager = $this->getServiceLocator()->get(\common_ext_ExtensionsManager::SERVICE_ID);
        if ($extManager->isInstalled('taoProctoring')){
            $reflect = new \ReflectionClass(\oat\taoProctoring\model\execution\DeliveryExecution::class);
        } else {
            $reflect = new \ReflectionClass(\oat\taoDelivery\model\execution\DeliveryExecutionInterface::class);
        }

        $constants = $reflect->getConstants();
        $statuses = [];
        foreach ($constants as $constantName => $constantValue){
            if (strpos($constantName, 'STATE_') !== false) {
                $statuses[] = $constantValue;
            }
        }

        foreach ($statuses as $status) {
           $this->getPersistence()->del(self::USER_EXECUTIONS_PREFIX . $userUri . $status);
        }

        $deletedDe = $this->getPersistence()->del($deUri);
        $deletedUE = $this->getPersistence()->del(self::USER_DELIVERY_PREFIX . $userUri . $deliveryUri);

        return $deletedDe && $deletedUE;
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

    /**
     * @param KVDeliveryExecution $deliveryExecution
     * @param $old
     * @param $new
     */
    private function fixStatus(KVDeliveryExecution $deliveryExecution, $old, $new)
    {
        $userId = $deliveryExecution->getUserIdentifier();
        $oldData = $this->getPersistence()->get(self::USER_EXECUTIONS_PREFIX . $userId . $old);
        $oldStateKeys = $oldData !== false ? json_decode($oldData) : [];
        $oldStateExecutions = [];
        if (is_array($oldStateKeys)) {
            foreach ($oldStateKeys as $key) {
                $de = $this->getDeliveryExecution($key);
                if ($de->getIdentifier() !== $deliveryExecution->getIdentifier()) {
                    $oldStateExecutions[$de->getIdentifier()] = $de;
                }
            }
            $this->setDeliveryExecutions($userId, $old, $oldStateExecutions);
        }

        $newData = $this->getPersistence()->get(self::USER_EXECUTIONS_PREFIX . $userId . $new);
        $newStateKeys = $newData !== false ? json_decode($newData) : [];
        $newStateExecutions = [];
        if (is_array($newStateKeys) && !isset($newStateKeys[$deliveryExecution->getIdentifier()])) {
            foreach ($newStateKeys as $key) {
                $de = $this->getDeliveryExecution($key);
                $newStateExecutions[$de->getIdentifier()] = $de;
            }
            $newStateExecutions[$deliveryExecution->getIdentifier()] = $deliveryExecution;
        }
        $this->setDeliveryExecutions($userId, $new, $newStateExecutions);
    }
}
