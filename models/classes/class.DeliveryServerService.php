<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

require_once('taoDelivery/models/classes/class.DeliveryService.php');

/**
 * taoDelivery_models_classes_DeliveryService
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_DeliveryServerService
    extends taoDelivery_models_classes_DeliveryService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
   
		
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct()
    {
		parent::__construct();
    }
			
	 /**
     * Check the login/pass in a MySQL table to identify a subject when he/she takes the delivery.
     * This method is used in the Delivery Server and uses direct access to the database for performance purposes.
	 * It returns the uri of the identified subjectm and an empty string otherwise.
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string login
	 * @param  string password
     * @return object
     */
	public function checkSubjectLogin($login, $password){
	
		$returnValue = null;
		
		$subjectsByLogin=core_kernel_classes_ApiModelOO::singleton()->getSubject(SUBJECT_LOGIN_PROP , $login);
		$subjectsByPassword=core_kernel_classes_ApiModelOO::singleton()->getSubject(SUBJECT_PASSWORD_PROP , $password);
		
		$subjects = $subjectsByLogin->intersect($subjectsByPassword);
		
		if($subjects->count()>0){
			//TODO: unicity of login/password pair to be implemented
			$returnValue = $subjects->get(0);
		}
		
		return $returnValue;
	}
	
	/**
     * Get all tests available for the identified subject.
     * This method is used in the Delivery Server and uses direct access to the database for performance purposes.
	 * It returns an array containing the uri of selected tests or an empty array otherwise.
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string subjectUri
     * @return array
     */
	public function getTestsBySubject($subjectUri){//useless anymore
		
		$returnValue=array();
				
		$groups=core_kernel_classes_ApiModelOO::singleton()->getSubject('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members' , $subjectUri);
		$deliveries = new core_kernel_classes_ContainerCollection(new common_Object());
		foreach ($groups->getIterator() as $group) {
			$deliveries = $deliveries->union(core_kernel_classes_ApiModelOO::singleton()->getObject($group->uriResource, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Tests'));
		}
		//TODO: eliminate duplicate deliveries (with a function like unique_array() ):
		$returnValue = $deliveries;
		
		return $returnValue;
	}
	
	/**
     * Get all deliveries available for the identified subject.
     * This method is used on the Delivery Server and uses direct access to the database for performance purposes.
	 * It returns an array containing the uri of selected deliveries or an empty array otherwise.
	 * To be tested when core_kernel_classes_ApiModelOO::getObject() is implemented
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string subjectUri
     * @return array
     */
	public function getDeliveriesBySubject($subjectUri){
		
		$returnValue=array();
		
		$groups = core_kernel_classes_ApiModelOO::singleton()->getSubject('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members' , $subjectUri);
		$deliveries = new core_kernel_classes_ContainerCollection(new common_Object());
		foreach ($groups->getIterator() as $group) {
			$deliveries = $deliveries->union(core_kernel_classes_ApiModelOO::singleton()->getObject($group->uriResource, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries'));
		}
		//TODO: eliminate duplicate deliveries (with a function like unique_array() ):
		$returnValue = $deliveries;
		
		
		return $returnValue;
	}
			
	/**
     * The method checks if the current time against the values of the properties PeriodStart and PeriodEnd.
	 * It returns true if the delivery execution period is valid at the current time.
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource aDeliveryInstance
     * @return boolean
     */
	public function checkPeriod(core_kernel_classes_Resource $aDeliveryInstance){
		// http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart
		// http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd
		$validPeriod=false;
		
		//supposing that the literal value saved in the properties is in the right format: YYYY-MM-DD HH:MM:SS or YYYY-MM-DD
		$startDate=null;
		foreach ($aDeliveryInstance->getPropertyValuesCollection(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart'))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Literal ){
				if(!empty($value->literal)){
					$startDate = date_create($value->literal);
					break;
				}
			}
		}
		
		$endDate=null;
		foreach ($aDeliveryInstance->getPropertyValuesCollection(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd'))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Literal ){
				if(!empty($value->literal)){
					$endDate = date_create($value->literal);
					break;
				}
			}
		}
		// var_dump($startDate);var_dump($endDate);var_dump( date_create('2010-03-01') );die();
		if(!empty($startDate)){
			if($endDate) {$validPeriod = (date_create()>=$startDate and date_create()<=$endDate); }
			else  {$validPeriod = (date_create()>=$startDate);}
		}else{
			if(!empty($endDate)) {$validPeriod = (date_create()<=$endDate);}
			else $validPeriod = true;
		}
		// throw new Exception("hjgfghm".$validPeriod." ");
		// var_dump($validPeriod);die();
		return $validPeriod;
	}
	
	
	
	/**
     * add history of delivery execution in the ontology
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string delivery
	 * @param  string subject
     * @return void
     */
	public function addHistory(core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $subject){

		if(empty($subject)) throw new Exception("the subject cannot be empty");
		if(empty($delivery)) throw new Exception("the delivery cannot be empty");
		$history = $this->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_HISTORY_CLASS), "Execution of {$delivery->getLabel()} by {$subject->getLabel()}");

		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP), $subject->uriResource);
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_DELIVERY_PROP), $delivery->uriResource);
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP), time() );

	}
	
	/**
     * Check if the subject is set as excluded from the delivery execution
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource subject
     * @param  core_kernel_classes_Resource delivery
	 * @return boolean
     */
	public function isExcludedSubject(core_kernel_classes_Resource $subject, core_kernel_classes_Resource $delivery){
		
		$returnValue = false;
		
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
		
		return $returnValue;
	}
	
	/**
     * Get the maximal number of execution for a delivery
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
	 * @return int
     */
	public function getMaxExec(core_kernel_classes_Resource $delivery){
		
		$returnValue = -1;
		
		if(!is_null($delivery)){
			$maxExec = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP));
			if($maxExec instanceof core_kernel_classes_Literal){
				if( trim($maxExec->literal) != '' ){
					$returnValue = intval($maxExec->literal);
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
     * Get the list of available deliveries for a given subject.
     * When the option "check" is set to true, it performs required checks to filter the deliveries the subject is allowed to execute.
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource subject
     * @param  boolean true
	 * @return array
     */
	public function getDeliveries(core_kernel_classes_Resource $subject, $check = true){
		//get list of available deliveries for this subject:
		try{
			$deliveriesCollection = $this->getDeliveriesBySubject($subject->uriResource);
		}catch(Exception $e){
			echo "error: ".$e->getMessage();
		}

		$deliveries = array(
			'notCompiled' => array(),
			'noResultServer' => array(),
			'subjectExcluded' => array(),
			'wrongPeriod' => array(),
			'maxExecExceeded' => array(),
			'ok' => array()
		);

		foreach($deliveriesCollection->getIterator() as $delivery){

			if($check){

				//check if it is compiled:
				try{
					$isCompiled = $this->isCompiled($delivery);
				}catch(Exception $e){
					echo "error: ".$e->getMessage();
				}
				if(!$isCompiled){
					$deliveries['notCompiled'][] = $delivery;
					continue;
				}

				// check if it has valid resultServer defined:
				try{
					$resultServer = $this->getResultServer($delivery);

				}catch(Exception $e){
					echo "error: ".$e->getMessage();
				}
				if(empty($resultServer)){
					$deliveries['noResultServer'][] = $delivery;
					continue;
				}

				//check if the subject is excluded:
				try{
					$isExcluded = $this->isExcludedSubject($subject, $delivery);
				}catch(Exception $e){
					echo "error: ".$e->getMessage();
				}
				if($isExcluded){
					$deliveries['subjectExcluded'][] = $delivery;
					continue;
				}

				//check the period
				try{
					$isRightPeriod = $this->checkPeriod($delivery);
				}catch(Exception $e){
					echo "error$isRightPeriod: ".$e->getMessage();
				}
				if(!$isRightPeriod){
					$deliveries['wrongPeriod'][] = $delivery;
					continue;
				}
				
				//check maxexec: how many times the subject has already taken the current delivery?
				$maxExec = $this->getMaxExec($delivery);
				if($maxExec>=0){//check only is the value is defined. If no value for maxexec is defined, the returned value for getMaxExec is -1
					try{
						$historyCollection = $this->getHistory($delivery, $subject);
					}catch(Exception $e){
						echo "error: ".$e->getMessage();
					}
				
					if(!$historyCollection->isEmpty()){
						if($historyCollection->count() >= $maxExec ){
							$deliveries['maxExecExceeded'][] = $delivery;
							continue;
						}
					}
				}
			}//endif of "check"

			//all check performed:
			$deliveries['ok'][] = $delivery; //the process uri is contained in the property DeliveryContent of the delivery
		}

		$availableProcessDefinition = array();
		foreach($deliveries['ok'] as $availableDelivery){
			if($check) {
				$availableProcessDefinition[ $availableDelivery->uriResource ] = $availableDelivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
			}
			else{
				$res = $availableDelivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
				if($res !=null) {
					$availableProcessDefinition[ $availableDelivery->uriResource ] = $res->uriResource;
				}
			}
		}
		// var_dump($deliveries);

		//return this array to the workflow controller: extended from main
		return $availableProcessDefinition;
	}
	

} /* end of class taoDelivery_models_classes_DeliveryServerService */

?>