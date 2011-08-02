<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery/models/classes/class.DeliveryServerService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 01.07.2011, 14:08:35 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoDelivery_models_classes_DeliveryService
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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

		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP), $subject->uriResource);
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_DELIVERY_PROP), $delivery->uriResource);
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP), time() );
                $history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_PROCESS_INSTANCE), $processInstance->uriResource);
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002067 end
    }

    /**
     * The method checks if the current time against the values of the
     * PeriodStart and PeriodEnd.
     * It returns true if the delivery execution period is valid at the current
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
			foreach ($delivery->getPropertyValuesCollection(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart'))->getIterator() as $value){
				if($value instanceof core_kernel_classes_Literal ){
					if(!empty($value->literal)){
						$startDate = date_create($value->literal);
						break;
					}
				}
			}
			
			$endDate=null;
			foreach ($delivery->getPropertyValuesCollection(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd'))->getIterator() as $value){
				if($value instanceof core_kernel_classes_Literal ){
					if(!empty($value->literal)){
						$endDate = date_create($value->literal);
						break;
					}
				}
			}
			
			// var_dump($startDate);var_dump($endDate);var_dump( date_create('2010-03-01') );die();
			if(!empty($startDate)){
				if(!empty($endDate)) {$returnValue = (date_create()>=$startDate and date_create()<=$endDate); }
				else  {$returnValue = (date_create()>=$startDate);}
			}else{
				if(!empty($endDate)) {$returnValue = (date_create()<=$endDate);}
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
							echo "error: ".$e->getMessage();
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
				$returnValue[ $availableDelivery->uriResource ] = (($check) ? $deliveryProcess : $deliveryProcess->uriResource);
			}
		}
		
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000206D end

        return (array) $returnValue;
    }

    /**
     * Get the maximal number of execution for a delivery
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
			if($excludedSubject == $subject->uriResource){
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * Short description of method checkExcludedSubject
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * Get all deliveries available for the identified subject.
     * This method is used on the Delivery Server and uses direct access to the
     * for performance purposes.
     * It returns an array containing the uri of selected deliveries or an empty
     * otherwise.
     * To be tested when core_kernel_impl_ApiModelOO::getObject() is implemented
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource subject
     * @return array
     */
    public function getDeliveriesBySubject( core_kernel_classes_Resource $subject)
    {
        $returnValue = array();

        // section 10-13-1-39-2ec7ed43:12e6c7e48bb:-8000:0000000000002C3A begin
		$propGroupDeliveries = new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
		$groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		$groups = $groupClass->searchInstances(array(TAO_GROUP_MEMBERS_PROP => $subject->uriResource), array('like'=>false, 'recursive' => true));
		
		
		
		$deliveries = array();
		foreach ($groups as $group) {
			$deliveryCollection = $group->getPropertyValuesCollection($propGroupDeliveries);
			foreach($deliveryCollection->getIterator() as $delivery){
				$deliveries[$delivery->uriResource] = $delivery;
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource currentUser
     * @return array
     */
    public function getStartedProcessExecutions( core_kernel_classes_Resource $currentUser = null)
    {
        $returnValue = array();

        // section 127-0-1-1--62c951b2:130e595e292:-8000:0000000000002F5A begin
        
        $tokenClass = new core_kernel_classes_Class(CLASS_TOKEN);
        $propTokenActivityExec = new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITYEXECUTION);
        $propActivityExecProcessExec = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
        $propProcessExecExecutionOf = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
        $currentUserTokens = $tokenClass->searchInstances(array(PROPERTY_TOKEN_CURRENTUSER => $currentUser->uriResource));
                
        if(!is_null($currentUser)){
                
        // Get the activities where the user has an active token.
		foreach ($currentUserTokens as $token) {
			$validToken = false;
            $activityExecution = $token->getOnePropertyValue($propTokenActivityExec);
			$processExecution = $activityExecution->getOnePropertyValue($propActivityExecProcessExec);
			if($processExecution instanceof core_kernel_classes_Resource && $processExecution->exists()){
				$processDefinition = $processExecution->getOnePropertyValue($propProcessExecExecutionOf);
				if($processDefinition instanceof core_kernel_classes_Resource && $processDefinition->exists()){
						$validToken = true;
				}
			}

			if($validToken){
					$returnValue[] = new wfEngine_models_classes_ProcessExecution($processExecution->uriResource);
			}else{
					$token->delete();
			}
		}
                
        }
        
        // section 127-0-1-1--62c951b2:130e595e292:-8000:0000000000002F5A end

        return (array) $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryServerService */

?>