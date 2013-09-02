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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryExecutionService extends tao_models_classes_GenerisService
{

    public function getAvailableDeliveries($userUri)
    {
        $deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
        $groups = taoGroups_models_classes_GroupsService::singleton()->getGroups($userUri);
        
        $deliveryCandidates = array();
        foreach ($groups as $group) {
            foreach($deliveryService->getDeliveriesByGroup($group) as $delivery) {
                $deliveryCandidates[$delivery->getUri()] = $delivery;
            }
        }

        // check if realy available
        $compiledDeliveries = array();
        foreach ($deliveryCandidates as $candidate) {
            $compiled = taoDelivery_models_classes_CompilationService::singleton()->getCompiledContent($candidate);
            // compiled?
            if (empty($compiled)) {
                continue;
            }
            // status?
            // resultserver?
            // period?
            // excluded
            // max executions 
            $compiledDeliveries[] = $compiled;
        }
        return $compiledDeliveries;
    }

    public function getStartedDeliveries($userResource)
    {
        return array();
    }

    /**
     * @param core_kernel_classes_Resource $compiled
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $compiled, $userUri)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $execution = $executionClass->createInstanceWithProperties(array(
            PROPERTY_DELVIERYEXECUTION_DELIVERY   => $compiled,
            PROPERTY_DELVIERYEXECUTION_SUBJECT    => $userUri,
            PROPERTY_DELVIERYEXECUTION_START      => time(),
            PROPERTY_DELVIERYEXECUTION_STATUS     => INSTANCE_DELIVERYEXEC_ACTIVE        	
        ));
    }
}