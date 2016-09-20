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

use oat\taoDelivery\models\classes\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecution as InterfaceDeliveryExecution;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @package taoDelivery
 */
class taoDelivery_models_classes_execution_AdvancedKeyValueService extends taoDelivery_models_classes_execution_KeyValueService
{

    const USER_DELIVERY_PREFIX = 'kve_ud_';

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
     * @param string $userUri
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $userId)
    {
        $deliveryExecution = parent::initDeliveryExecution($assembly, $userId);
        $this->addDeliveryToUserExecutionList($userId, $assembly->getUri(), $deliveryExecution->getIdentifier());
        return $deliveryExecution;
    }

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

}
