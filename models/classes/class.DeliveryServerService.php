<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery/models/classes/class.DeliveryServerService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 22.02.2013, 16:31:42 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * returns the folder to store the compiled delivery
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
require_once('taoDelivery/models/classes/class.DeliveryService.php');

/* user defined includes */
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201E-includes begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201E-includes end

/* user defined constants */
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201E-constants begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201E-constants end

/**
 * Short description of class taoDelivery_models_classes_DeliveryServerService
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryServerService
    extends taoDelivery_models_classes_DeliveryService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return taoDelivery_models_classes_DeliveryServerService
     */
    public function __construct()
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002065 begin
		parent::__construct();
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002065 end

        return $returnValue;
    }

    /**
     * add history of delivery execution in the ontology
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @param  Resource subject
     * @param  Resource processInstance
     * @return mixed
     */
    public function addHistory( core_kernel_classes_Resource $delivery,  core_kernel_classes_Resource $subject,  core_kernel_classes_Resource $processInstance)
    {
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002067 begin
		if(empty($subject)) throw new Exception("the subject cannot be empty");
		if(empty($delivery)) throw new Exception("the delivery cannot be empty");

		$deliveryHistoryClass = new core_kernel_classes_Class(TAO_DELIVERY_HISTORY_CLASS);
		$history = $deliveryHistoryClass->createInstance("Execution of the delivery {$delivery->getLabel()} by {$subject->getLabel()} on ". date(DATE_ISO8601), "created by DeliveryServerService on ". date(DATE_ISO8601));

		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP), $subject->getUri());
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_DELIVERY_PROP), $delivery->getUri());
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP), time() );
                $history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_PROCESS_INSTANCE), $processInstance->getUri());
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002067 end
    }

    /**
     * The method checks if the current time against the values of the
     * PeriodStart and PeriodEnd.
     * It returns true if the delivery execution period is valid at the current
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array param
     * @return boolean
     */
    public function checkPeriod($param = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002069 begin
		if($this->hasParameters($param, array('delivery'))){
			
			$delivery = $param['delivery'];
			
			//supposing that the literal value saved in the properties is in the right format: YYYY-MM-DD HH:MM:SS or YYYY-MM-DD
			$startDate=null;
			foreach ($delivery->getPropertyValuesCollection(new core_kernel_classes_Property(TAO_DELIVERY_START_PROP))->getIterator() as $value){
				if($value instanceof core_kernel_classes_Literal ){
					if(!empty($value->literal)){
						$startDate = date_create($value->literal);
						break;
					}
				}
			}
			
			$endDate=null;
			foreach ($delivery->getPropertyValuesCollection(new core_kernel_classes_Property(TAO_DELIVERY_END_PROP))->getIterator() as $value){
				if($value instanceof core_kernel_classes_Literal ){
					if(!empty($value->literal)){
						$endDate = date_create($value->literal);
						break;
					}
				}
			}
			
			if(!empty($startDate)){
				if(!empty($endDate)){
				    $endDate->add(new DateInterval("P1D"));
				    $returnValue = (date_create()>=$startDate and date_create()<=$endDate); 
                }
				else{
				    $returnValue = (date_create()>=$startDate);
                }
			}else{
				if(!empty($endDate)){
				    $endDate->add(new DateInterval("P1D"));
				    $returnValue = (date_create()<=$endDate);
                }
				else $returnValue = true;
			}
			
		}
		
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002069 end

        return (bool) $returnValue;
    }

    /**
     * Get the list of available deliveries for a given subject.
     * When the option "check" is set to true, it performs required checks to
     * the deliveries the subject is allowed to execute.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource subject
     * @param  boolean check
     * @param  array checkList
     * @return array
     */
    public function getDeliveries( core_kernel_classes_Resource $subject, $check = true, $checkList = array())
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000206D begin
		
		//get list of available deliveries for this subject:
		try{
			$deliveriesBySubject = $this->getDeliveriesBySubject($subject);
		}catch(Exception $e){
			common_Logger::e('Could not get Deliveries for subject '.$subject->getUri(), 'DELIVERY');
			echo "error: ".$e->getMessage();
		}
			
		
		$deliveries = array();
		$deliveries['ok'] = array();
		
		$checkFunctions = array();
		if($check){
			$classMethods = get_class_methods(get_class($this));
			foreach($classMethods as $functionName){
				if(preg_match('/^check(.)+/', $functionName)){
					$checkFunctions[] = $functionName;
				}
			}
			
			if(!empty($checkList)){
				$checkFunctions = array_intersect($checkFunctions, $checkList);
			}
		}
		
		foreach($deliveriesBySubject as $delivery){
			
			if($check){
			
				foreach($checkFunctions as $function){
					if(method_exists($this, $function)){
						
						try{
							$ok = $this->$function(array(
								'delivery' => $delivery,
								'subject' => $subject
							));
						}catch(Exception $e){
							common_Logger::e("Error during delivery evaluation: ".$e->getMessage());
							$ok = false;
						}
						
						if(!$ok){
							if(!isset($deliveries[$function])){
								$deliveries[$function] = array();
							}
							$deliveries[$function][] = $delivery;
							continue 2;
						}
					}
				}
				
			}//endif of "check"
			
			//all check performed:
			$deliveries['ok'][] = $delivery; //the process uri is contained in the property DeliveryContent of the delivery
		}
		
		$propDeliveryProcess = new core_kernel_classes_Property(TAO_DELIVERY_PROCESS);
		foreach($deliveries['ok'] as $availableDelivery){
			$deliveryProcess = $availableDelivery->getOnePropertyValue($propDeliveryProcess);
			
			// /!\ Check if the $deliveryProcess is not a literal. If not compiled, we get an empty
			// literal and it produces an error.
			if($deliveryProcess != null && !$deliveryProcess instanceof core_kernel_classes_Literal) {
				$returnValue[ $availableDelivery->getUri() ] = (($check) ? $deliveryProcess : $deliveryProcess->getUri());
			}
		}
		
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000206D end

        return (array) $returnValue;
    }

    /**
     * Get the maximal number of execution for a delivery
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return int
     */
    public function getMaxExec( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (int) 0;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002071 begin
		$returnValue = -1;
		
		if(!is_null($delivery)){
			$maxExec = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP));
			if($maxExec instanceof core_kernel_classes_Literal){
				if( trim($maxExec->literal) != '' ){
					$returnValue = intval($maxExec->literal);
				}
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002071 end

        return (int) $returnValue;
    }

    /**
     * Check if the subject is set as excluded from the delivery execution
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource subject
     * @param  Resource delivery
     * @return boolean
     */
    public function isExcludedSubject( core_kernel_classes_Resource $subject,  core_kernel_classes_Resource $delivery = null)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002073 begin
		if(is_null($subject) || is_null($delivery)){
			return $returnValue;
		}
		
		$excludedSubjectArray = $this->getExcludedSubjects($delivery);
		foreach($excludedSubjectArray as $excludedSubject){
			if($excludedSubject == $subject->getUri()){
				$returnValue = true;
				break;
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002073 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkCompiled
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array param
     * @return boolean
     */
    public function checkCompiled($param = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C32 begin
		if($this->hasParameters($param, array('delivery'))){
			$delivery = $param['delivery'];
			$returnValue = $this->isCompiled($delivery);
		}
        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C32 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkResultServer
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array param
     * @return boolean
     */
    public function checkResultServer($param = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C34 begin
		if($this->hasParameters($param, array('delivery'))){
			$delivery = $param['delivery'];
			$resultServer = $this->getResultServer($delivery);
			if(!empty($resultServer)){
				$returnValue = true;
			}
		}
        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C34 end

        return (bool) $returnValue;
    }
    /**
     * initalize the resultserver for a given execution
     * @param core_kernel_classes_resource processExecution
     */
    public function initResultServer($processExecution, $resultServerCallOverrideParameters =array()){

        //starts or resume a taoResultServerStateFull session for results submission

        //retrieve the resultServer definition that is related to this delivery to be used
        $delivery = $this->getDelivery($processExecution);
        //retrieve the result server definition
        $resultServer = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        //callOptions are required in the case of a LTI basic storage
       
        taoResultServer_models_classes_ResultServerStateFull::singleton()->initResultServer($resultServer->getUri(), $resultServerCallOverrideParameters);

        //a unique identifier for data collected through this delivery execution
        //in the case of LTI, we should use the sourceId
        $resultIdentifier = (isset($resultServerCallOverrideParameters["resultIdentifier"])) ? $resultServerCallOverrideParameters["lis_result_sourcedid"] :$processExecution->getUri();
        //the dependency to taoResultServer should be re-thinked with respect to a delivery level proxy
        taoResultServer_models_classes_ResultServerStateFull::singleton()->spawnResult($resultIdentifier);

        //set up the related test taker
        //a unique identifier for the test taker
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedTestTaker(wfEngine_models_classes_UserService::singleton()->getCurrentUser()->getUri());

         //a unique identifier for the delivery
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedDelivery($delivery->getUri());

    }
    public function getDelivery($processExecution){
        $processDefinition = wfEngine_models_classes_ProcessExecutionService::singleton()->getExecutionOf($processExecution);
        $deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
        $deliveries = $deliveryClass->searchInstances(
        	array(TAO_DELIVERY_PROCESS	=> $processDefinition->getUri()),
        	array('recursive' => true, 'like' => false)
         );
        return current($deliveries);
    }
    /**
     * Short description of method checkExcludedSubject
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array param
     * @return boolean
     */
    public function checkExcludedSubject($param = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C36 begin
		$returnValue = true;
		if($this->hasParameters($param, array('delivery', 'subject'))){
			$subject = $param['subject'];
			$delivery = $param['delivery'];
		
			$returnValue = $this->isExcludedSubject($subject, $delivery)?false:true;
		}
        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C36 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkMaxExecution
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array param
     * @return boolean
     */
    public function checkMaxExecution($param = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C38 begin
		if($this->hasParameters($param, array('delivery', 'subject'))){
			$returnValue = true;
			$delivery = $param['delivery'];
			$subject = $param['subject'];
			
			$maxExec = $this->getMaxExec($delivery);
			if($maxExec>=0){
				$histories = $this->getHistory($delivery, $subject);
				if(count($histories)){
					if(count($histories) >= $maxExec ){
						$returnValue = false;
					}
				}else{
					if($maxExec == 0){
						$returnValue = false;
					}
				}
			}
		}
        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C38 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkDeliveryStatus
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array param
     * @return boolean
     */
    public function checkDeliveryStatus($param = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--64338aa:1369b89eee1:-8000:00000000000038F3 begin
   		if($this->hasParameters($param, array('delivery'))){
			$delivery = $param['delivery'];
			$status = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_ACTIVE_PROP));
			if ($status->getUri() == GENERIS_TRUE) {
				$returnValue = true;
			}
		} else {
			throw new common_exception_Error('checkDeliveryStatus called without delivery');
		}
        // section 127-0-1-1--64338aa:1369b89eee1:-8000:00000000000038F3 end

        return (bool) $returnValue;
    }

    /**
     * Get all deliveries available for the identified subject.
     * This method is used on the Delivery Server and uses direct access to the
     * for performance purposes.
     * It returns an array containing the uri of selected deliveries or an empty
     * otherwise.
     * To be tested when core_kernel_impl_ApiModelOO::getObject() is implemented
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource subject
     * @return array
     */
    public function getDeliveriesBySubject( core_kernel_classes_Resource $subject)
    {
        $returnValue = array();

        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C3A begin
		$propGroupDeliveries = new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
		$groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		$groups = $groupClass->searchInstances(array(TAO_GROUP_MEMBERS_PROP => $subject->getUri()), array('like'=>false, 'recursive' => 1000));
		
		$deliveries = array();
		foreach ($groups as $group) {
			$deliveryCollection = $group->getPropertyValuesCollection($propGroupDeliveries);
			foreach($deliveryCollection->getIterator() as $delivery){
				$deliveries[$delivery->getUri()] = $delivery;
			}
		}
		
		$returnValue = $deliveries;
        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C3A end

        return (array) $returnValue;
    }

    /**
     * Short description of method hasParameters
     *
     * @access protected
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array params
     * @param  array keys
     * @return boolean
     */
    protected function hasParameters($params = array(), $keys = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C6E begin
		foreach($keys as $key){
			if(isset($params[$key])){
				if($params[$key] instanceof core_kernel_classes_Resource){
					$returnValue = true;
				}
			}
			if(!$returnValue) break;//return false directly
		}
        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C6E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getStartedProcessExecutions
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource currentUser
     * @return array
     */
    public function getStartedProcessExecutions( core_kernel_classes_Resource $currentUser)
    {
        $returnValue = array();

        // section 127-0-1-1--62c951b2:130e595e292:-8000:0000000000002F5A begin
		$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$currentUserActivityExecutions = $activityExecutionClass->searchInstances(array(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER => $currentUser->getUri()), array('like'=>false));
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		
		foreach($currentUserActivityExecutions as $currentUserActivityExecution){
			
			$validExecution = false;
			$processExecution = null;
			try{
				$processExecution = $activityExecutionService->getRelatedProcessExecution($currentUserActivityExecution);
			}catch(wfEngine_models_classes_ProcessExecutionException $e){}
			
			if(!is_null($processExecution)){
				$processDefinition = null;
				try{
					$processDefinition = $processExecutionService->getExecutionOf($processExecution);
				}catch(wfEngine_models_classes_ProcessExecutionException $e){}
				
				if($processDefinition instanceof core_kernel_classes_Resource && $processDefinition->exists()){
					$validExecution = true;
				}
			}
			
			if($validExecution){
				$returnValue[$processExecution->getUri()] = $processExecution;
			}else{
				$currentUserActivityExecution->delete();
			}
		}
        // section 127-0-1-1--62c951b2:130e595e292:-8000:0000000000002F5A end

        return (array) $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryServerService */

?>