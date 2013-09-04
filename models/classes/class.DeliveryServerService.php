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
class taoDelivery_models_classes_DeliveryServerService extends tao_models_classes_GenerisService
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

    /**
     * initalize the resultserver for a given execution
     * @param core_kernel_classes_resource processExecution
     */
    public function initResultServer($compiledDelivery, $executionIdentifier){

        //starts or resume a taoResultServerStateFull session for results submission

        //retrieve the resultServer definition that is related to this delivery to be used
        $delivery = $this->getDeliveryFromCompiledDelivery($compiledDelivery);
        //retrieve the result server definition
        $resultServer = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        //callOptions are required in the case of a LTI basic storage

        taoResultServer_models_classes_ResultServerStateFull::singleton()->initResultServer($resultServer->getUri());

        //a unique identifier for data collected through this delivery execution
        //in the case of LTI, we should use the sourceId

        //the dependency to taoResultServer should be re-thinked with respect to a delivery level proxy
        taoResultServer_models_classes_ResultServerStateFull::singleton()->spawnResult($executionIdentifier);
         common_Logger::i("Spawning/resuming result identifier related to process execution ".$executionIdentifier);
        //set up the related test taker
        //a unique identifier for the test taker
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedTestTaker(wfEngine_models_classes_UserService::singleton()->getCurrentUser()->getUri());

         //a unique identifier for the delivery
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedDelivery($delivery->getUri());
    }
    private function getDeliveryFromCompiledDelivery(core_kernel_classes_Resource $compiledDelivery) {
        $deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
        $deliveries = $deliveryClass->searchInstances(array(PROPERTY_DELIVERY_COMPILED => $compiledDelivery->getUri()));
       
        if (count($deliveries)!=1) {
            throw new common_Exception("0 or more tha one delivery is associated with the compiledDelviery  ".$compiledDelivery->getUri());
        }
        return array_shift($deliveries);
    }
}