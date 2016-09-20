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
 *
 */
class taoDelivery_models_classes_execution_AdvancedKVDeliveryExecution extends taoDelivery_models_classes_execution_KVDeliveryExecution
{

    const USER_DELIVERY_PREFIX = 'kve_ud_';


    /**
     *
     * @param common_persistence_KeyValuePersistence $persistence
     * @param string $userId
     * @param core_kernel_classes_Resource $assembly
     * @return DeliveryExecution
     */
    public static function spawn(
        common_persistence_KeyValuePersistence $persistence,
        $userId,
        core_kernel_classes_Resource $assembly
    ) {
        $identifier = self::DELIVERY_EXECUTION_PREFIX . common_Utils::getNewUri();
        $params = array(
            RDFS_LABEL => $assembly->getLabel(),
            PROPERTY_DELVIERYEXECUTION_DELIVERY => $assembly->getUri(),
            PROPERTY_DELVIERYEXECUTION_SUBJECT => $userId,
            PROPERTY_DELVIERYEXECUTION_START => microtime(),
            PROPERTY_DELVIERYEXECUTION_STATUS => InterfaceDeliveryExecution::STATE_ACTIVE
        );
        $kvDe = new static($persistence, $identifier, $params);
        $kvDe->save();

        $kvDe->addDeliveryToUserExecutionList($userId, $assembly->getUri(), $identifier);

        $de = new DeliveryExecution($kvDe);
        return $de;
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


}
