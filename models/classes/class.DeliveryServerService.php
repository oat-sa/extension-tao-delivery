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

    /**
     * Return all available (assigned and compiled) deliveries for the userUri.
     * Delivery settings are returned to identify when and how many tokens are left
     * for this delivery
     */
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
            // period?
            // excluded
            // max executions

            $deliverySettings = $this->getDeliverySettings($candidate);
            $deliverySettings["TAO_DELIVERY_USED_TOKENS"] = $this->getDeliveryUsedTokens($compiled, $userUri);
            $deliverySettings["TAO_DELIVERY_TAKABLE"] = $this->isDeliveryExecutionAllowed($compiled, $userUri);
            $compiledDeliveries[] = array(
                "compiledDelivery"  =>$compiled,
                "settingsDelivery"  =>$deliverySettings
                );
        }
       
        return $compiledDeliveries;
    }
    public function getDeliverySettings(core_kernel_classes_Resource $delivery){
        $deliveryProps = $delivery->getPropertiesValues(array(
            new core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP),
            new core_kernel_classes_Property(TAO_DELIVERY_START_PROP),
            new core_kernel_classes_Property(TAO_DELIVERY_END_PROP),
            //new core_kernel_classes_Property( TAO_DELIVERY_ACTIVE_PROP)
        ));

        $propMaxExec = current($deliveryProps[TAO_DELIVERY_MAXEXEC_PROP]);
        $propStartExec = current($deliveryProps[TAO_DELIVERY_START_PROP]);
        $propEndExec = current($deliveryProps[TAO_DELIVERY_END_PROP]);

        $settings[TAO_DELIVERY_MAXEXEC_PROP] = (!(is_object($propMaxExec)) or ($propMaxExec=="")) ? 0 : $propMaxExec->literal;
        $settings[TAO_DELIVERY_START_PROP] = (!(is_object($propStartExec)) or ($propStartExec=="")) ? null : $propStartExec->literal;
        $settings[TAO_DELIVERY_END_PROP] = (!(is_object($propEndExec)) or ($propEndExec=="")) ? null : $propEndExec->literal;

        //$settings[TAO_DELIVERY_ACTIVE_PROP] = current($deliveryProps["TAO_DELIVERY_END_PROP"])->getUri();
        /*
        if (
            (!isset($settings[TAO_DELIVERY_MAXEXEC_PROP])) or
            (is_null($settings[TAO_DELIVERY_MAXEXEC_PROP])) or
            (count($settings[TAO_DELIVERY_MAXEXEC_PROP])==0)) or{
         */

        return $settings;
    }

    public function getDeliveryUsedTokens(core_kernel_classes_Resource $compiled, $userUri){
        return count($this->getDeliveryExecutions($compiled, $userUri));
    }
    public function getDeliveryExecutions(core_kernel_classes_Resource $compiled, $userUri)
    {   
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $deliveryExecutions = $executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_SUBJECT  => $userUri,
            PROPERTY_DELVIERYEXECUTION_DELIVERY => $compiled->getUri()
        ), array(
        	'like' => false
        ));

        return $deliveryExecutions;
    }
    public function isDeliveryExecutionAllowed(core_kernel_classes_Resource $compiled, $userUri){

        $deliveryClass = taoDelivery_models_classes_DeliveryService::singleton()->getRootClass();
        $deliveries = $deliveryClass->searchInstances(array(
            PROPERTY_DELIVERY_COMPILED  => $compiled->getUri()
        ), array(
        	'like' => false
        ));
        if (count($deliveries)!=1) {
            common_Logger::f("Attempt to start the compiled delivery ".$compiled->getUri(). "related to 0 or >1 deliveries");
            return false;
        }
        $delivery = current($deliveries);
        $settings = $this->getDeliverySettings($delivery);

        //check Tokens
        $usedTokens = $this->getDeliveryUsedTokens($compiled, $userUri);
        
        if (($settings[TAO_DELIVERY_MAXEXEC_PROP] !=0 ) and ($usedTokens >= $settings[TAO_DELIVERY_MAXEXEC_PROP])) {
            common_Logger::f("Attempt to start the compiled delivery ".$compiled->getUri(). "without tokens");
            return false;
        }

        //check time
        $startDate  =    date_create($settings[TAO_DELIVERY_START_PROP]);
        $endDate    =    date_create($settings[TAO_DELIVERY_END_PROP]);
        if(!empty($startDate)){
				if(!empty($endDate)){
				    $endDate->add(new DateInterval("P1D"));
				    $dateCheck = (date_create()>=$startDate and date_create()<=$endDate);
                }
				else{
				    $dateCheck  = (date_create()>=$startDate);
                }
			}else{
				if(!empty($endDate)){
				    $endDate->add(new DateInterval("P1D"));
				    $dateCheck  = (date_create()<=$endDate);
                }
			}

        if (!($dateCheck )) {
             common_Logger::f("Attempt to start the compiled delivery ".$compiled->getUri(). " at the wrong date");
            return false;
        }
        return true;


        //check time

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
    public function getDeliveryFromCompiledDelivery(core_kernel_classes_Resource $compiledDelivery) {
        $deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
        $deliveries = $deliveryClass->searchInstances(array(PROPERTY_DELIVERY_COMPILED => $compiledDelivery->getUri()));
       
        if (count($deliveries)!=1) {
            throw new common_Exception("0 or more tha one delivery is associated with the compiledDelviery  ".$compiledDelivery->getUri());
        }
        return array_shift($deliveries);
    }
}