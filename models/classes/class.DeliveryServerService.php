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

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every service instances.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
// require_once('tao/models/classes/class.Service.php');

/**
 * The Precompilator class provides many useful methods to accomplish the test compilation task
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
// require_once('taoDelivery/helpers/class.Precompilator.php');

require_once('taoDelivery/models/classes/class.DeliveryService.php');

/**
 * The taoDelivery_models_classes_DeliveryService class provides methods to connect to several ontologies and interact with them.
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
		
		// $this->deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		// $this->testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		// $this->subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
		// $this->groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		
		// $this->loadOntologies($this->deliveryOntologies);
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
	public function getTestsBySubject($subjectUri){
		
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
     * The the url of the select result server
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource aDeliveryInstance
     * @return string
     */
	public function getResultServer(core_kernel_classes_Resource $aDeliveryInstance){
		
		$returnValue='';
		
		if(!is_null($aDeliveryInstance)){
		
			$aResultServerInstance = $aDeliveryInstance->getUniquePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer"));
			if($aResultServerInstance instanceof core_kernel_classes_Resource){
				//potential issue with the use of common_Utils::isUri in getPropertyValuesCollection() or store encoded url only in
				$resultServerUrl = $aResultServerInstance->getUniquePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl"));
				if($resultServerUrl instanceof core_kernel_classes_Literal){
					$returnValue = $resultServerUrl->literal;
				}
				if($resultServerUrl instanceof core_kernel_classes_Resource){
					$returnValue = $resultServerUrl->uriResource;
				}
			}
			
		}
		
		return $returnValue;
	}
	
	/**
     * add history of delivery execution in the ontology
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string deliveryUri
	 * @param  string subjectUri
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
	
	public function isExcludedSubject(core_kernel_classes_Resource $subject, core_kernel_classes_Resource $delivery){
		
		$returnValue = false;
		
		if(is_null($subject) || is_null($delivery)){
			return $returnValue;
		}
		
		$excludedSubjectArray = $this->getExcludedSubjects($delivery);
		foreach($excludedSubjectArray as $excludedSubject){
			if($excludedSubject == $subject->uriResource){
				$returnValue = true;
			}
		}
		
		return $returnValue;
	}
	
	public function getMaxExec(core_kernel_classes_Resource $delivery){
		
		$returnValue = -1;
		
		if(!is_null($delivery)){
			$maxExec = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP));
			if($maxExec instanceof core_kernel_classes_Literal){
				$returnValue = intval($maxExec->literal);
			}
		}
		
		return $returnValue;
	}
	
	

} /* end of class taoDelivery_models_classes_DeliveryServerService */

?>