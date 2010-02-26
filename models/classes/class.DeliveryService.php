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
require_once('tao/models/classes/class.Service.php');

/**
 * The Precompilator class provides many useful methods to accomplish the test compilation task
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('taoDelivery/helpers/class.Precompilator.php');

/**
 * The taoDelivery_models_classes_DeliveryService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_DeliveryService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The attribute deliveryClass contains the default TAO Delivery Class
     *
     * @access protected
     * @var Class
     */
    protected $deliveryClass = null;

	/**
     * The attribute testClass contains the default TAO Test Class
     *
     * @access protected
     * @var Class
     */
	protected $testClass = null;
	
	/**
     * The attribute subjectClass contains the default TAO Subject Class
     *
     * @access protected
     * @var Class
     */
	protected $subjectClass = null;
	
	/**
     * The attribute groupClass contains the default TAO Group Class
     *
     * @access protected
     * @var Class
     */
	protected $groupClass = null;
		
    /**
     * The attribute deliveryOntologies contains the reference to the TAODelivery Ontology
     *
     * @access protected
     * @var array
     */
    protected $deliveryOntologies = array(
		'http://www.tao.lu/Ontologies/TAODelivery.rdf',
		'http://www.tao.lu/Ontologies/TAOGroup.rdf',
		'http://www.tao.lu/Ontologies/TAOSubject.rdf',
		'http://www.tao.lu/Ontologies/TAOTest.rdf'
		);
	
	/**
     * The attribute groupsOntologies contains the reference to the TAOGroup Ontologies.
	 * It enables the DeliveryService Class to connect to them and fetch information about groups from them
     *
     * @access protected
     * @var array
     */
	protected $groupsOntologies = array('http://www.tao.lu/Ontologies/TAOGroup.rdf');
	
	/**
     * The attribute subjectsOntologies contains the reference to the TAOSubject Ontologies.
	 * It enables the DeliveryService Class to connect to them and fetch information about subjects from them
     *
     * @access protected
     * @var array
     */
	protected $subjectsOntologies = array('http://www.tao.lu/Ontologies/TAOSubject.rdf');
	
	/**
     * The attribute testsOntologies contains the reference to the TAOSubject Ontologies.
	 * It enables the DeliveryService Class to connect to them and fetch information about the tests from them
     *
     * @access protected
     * @var array
     */
	protected $testsOntologies = array('http://www.tao.lu/Ontologies/TAOTest.rdf');
	
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
		
		$this->deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		$this->testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$this->subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
		$this->groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		
		$this->loadOntologies($this->deliveryOntologies);
    }
	
	/**
     * The method getDeliveryClass return the current Delivery Class
	 * (not used yet in the current implementation)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getDeliveryClass($uri = '')
    {
        $returnValue = null;

		if(empty($uri) && !is_null($this->deliveryClass)){
			$returnValue = $this->deliveryClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isDeliveryClass($clazz)){
				$returnValue = $clazz;
			}
		}

        return $returnValue;
    }
	
	/**
     * The method getTestClass return the current Test Class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string uri
     * @return core_kernel_classes_Class
     */
	public function getTestClass($uri = '')
    {
        $returnValue = null;

		if(empty($uri) && !is_null($this->testClass)){
			$returnValue = $this->testClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isTestClass($clazz)){
				$returnValue = $clazz;
			}
		}

        return $returnValue;
    }
	
	/**
     * Returns a delivery by providing either its uri (default) or its label and the delivery class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getDelivery($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->deliveryClass;
		}
		if($this->isDeliveryClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		
        return $returnValue;
    }
	
	 /**
     * Create a new class of Delivery, which is basically always a subclass of an existing Delivery class.
	 * Require an array('propertyName' => 'propertyValue')
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createDeliveryClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->deliveryClass;
		}
		
		if($this->isDeliveryClass($clazz)){
		
			$deliveryClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $deliveryClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' delivery property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
			}
			$returnValue = $deliveryClass;
		}

        return $returnValue;
    }
	
	/**
     * Method to be called to delete a delivery instance
     * (Method is not used in the current implementation yet)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return boolean
     */
    public function deleteDelivery( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;
		
		if(!is_null($delivery)){
			//delete the process associated to the delivery:
			$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
			$processAuthoringService = new taoDelivery_models_classes_DeliveryAuthoringService();
			$processAuthoringService->deleteProcess($process);
			
			$returnValue = $delivery->delete();
		}

        return (bool) $returnValue;
    }

    /**
     * Method to be called to delete a delivery class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function deleteDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if(!is_null($clazz)){
			if($this->isDeliveryClass($clazz) && $clazz->uriResource != $this->deliveryClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Check whether the object is a delivery class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function isDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($clazz->uriResource == $this->deliveryClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->deliveryClass->getSubClasses(true) as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}

        return (bool) $returnValue;
    }
	
	/**
     * Check whether the object is a test class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
	public function isTestClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($clazz->uriResource == $this->testClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->testClass->getSubClasses() as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}

        return (bool) $returnValue;
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
		$returnValue=$deliveries;
		
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
     * The methods getTestStatus checks the value of the property "active" OR "compiled" for a given test instance (a ressource)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource aTestInstance
	 * @param  string status
     * @return boolean
     */
	public function getTestStatus($aTestInstance, $status){
		
		$returnValue=false;
		
		if(!($aTestInstance instanceof core_kernel_classes_Resource) ){
			throw new Exception("wrong resource in getTestStatus parameter");
			return $returnValue;
		}
		
		switch($status){
			case "active":
				$property=TEST_ACTIVE_PROP;
				break;
				
			case "compiled":
				$property=TEST_COMPILED_PROP;
				
				//check if the compiled folder exists:
				/*
				$testId=tao_helpers_Precompilator::getUniqueId($aTestInstance->uriResource);
				
				if(!is_dir(BASE_PATH."/compiled/$testId/")){
					return $returnValue;
				}*/ 
				break;
				
			default:
				throw new Exception("wrong test status parameter");
				return $returnValue;
		}
		
		foreach ($aTestInstance->getPropertyValuesCollection(new core_kernel_classes_Property($property))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Resource ){
				if ($value->uriResource == GENERIS_TRUE){
					$returnValue=true;
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
     * The method isCompiled checks the value of the property "compiled" for a given delivery instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource aDeliveryInstance
     * @return boolean
     */
	public function isCompiled(core_kernel_classes_Resource $aDeliveryInstance){
		
		$returnValue=false;
		
		if(!($aDeliveryInstance instanceof core_kernel_classes_Resource) ){
			throw new Exception("wrong resource in getTestStatus parameter");
			return $returnValue;
		}
		
		//could use the function getOnePropertyValue($prop, true) instead
		foreach ($aDeliveryInstance->getPropertyValuesCollection(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Resource ){
				if ($value->uriResource == GENERIS_TRUE){
					$returnValue=true;
					break;
				}
			}
		}
		
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
				$startDate = date_create($value->literal);
				break;
			}
		}
		
		$endDate=null;
		foreach ($aDeliveryInstance->getPropertyValuesCollection(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd'))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Literal ){
				$endDate = date_create($value->literal);
				break;
			}
		}
		
		if($startDate){
			if($endDate) $validPeriod = (date_create()>$startDate and date_create()<$endDate);
			else $validPeriod = (date_create()>$startDate);
		}else{
			if($endDate) $validPeriod = (date_create()<$endDate);
			else $validPeriod = true;
		}
		
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
		
		if(!is_null($delivery)){
		
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
     * get the list of excluded subjects of the delivery
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return array
     */
    public function getExcludedSubjects( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();
		
		if(!is_null($delivery)){
			$returnValue = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP));
		}
		
        return (array) $returnValue;
    }

    /**
     * define the list of the subjects that are excluded from a delivery
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @param  array subjects
     * @return boolean
     */
    public function setExcludedSubjects( core_kernel_classes_Resource $delivery, $subjects = array())
    {
        $returnValue = (bool) false;
		
		if(!is_null($delivery)){
			
			$memberProp = new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP);
			
			$delivery->removePropertyValues($memberProp);
			$done = 0;
			foreach($subjects as $subject){
				if($delivery->setPropertyValue($memberProp, $subject)){
					$done++;
				}
			}
			if($done == count($subjects)){
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }

    /**
     * get the list of tests in the delivery in parameter
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return array
     */
    public function getRelatedCampaigns( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();
		
		if(!is_null($delivery)){
			$returnValue = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP));
		}

        return (array) $returnValue;
    }

    /**
     * define the list of campaigns the delivery is associated to
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @param  array campaigns
     * @return boolean
     */
    public function setRelatedCampaigns( core_kernel_classes_Resource $delivery, $campaigns = array())
    {
        $returnValue = (bool) false;
		
		if(!is_null($delivery)){
			
			$campaignProp = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);
			
			$delivery->removePropertyValues($campaignProp);
			$done = 0;
			foreach($campaigns as $campaign){
				if($delivery->setPropertyValue($campaignProp, $campaign)){
					$done++;
				}
			}
			if($done == count($campaigns)){
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }
	
	/**
     * add history to
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string deliveryUri
	 * @param  string subjectUri
     * @return core_kernel_classes_ContainerCollection
     */
	public function getHistory($delivery, $subject = null){
	
		$historyCollection = new core_kernel_classes_ContainerCollection(new common_Object());
		
		if(empty($delivery)){
			throw new Exception("the delivery instance cannot be empty");
		}
		
		if(empty($subject)){
			//select History by delivery only (subject independent listing, i.e. select for all subjects)
			$historyCollection = core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_DELIVERY_PROP, $delivery->uriResource);
		}else{
			//select history by delivery and subject
			$historyByDelivery = core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_DELIVERY_PROP, $delivery->uriResource);
			$historyBySubject = core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_SUBJECT_PROP, $subject->uriResource);
			$historyCollection = $historyByDelivery->intersect($historyBySubject);
		}
		
		return $historyCollection;
		
		//note: for maxExec check on delivery server, simply make the following comparison: $this->getHistory($deliveryUri, $subjectUri)->count() < $deliveryMaxExec 
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
	public function addHistory($deliveryUri, $subjectUri){
		if(empty($subjectUri)) throw new Exception("the subject uri cannot be empty");
		if(empty($deliveryUri)) throw new Exception("the delivery uri cannot be empty");
		
		$history = $this->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_HISTORY_CLASS));
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP), $subjectUri);
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_DELIVERY_PROP), $deliveryUri);
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP), time() );
	}
	
	public function createInstance(core_kernel_classes_Class $clazz, $label = ''){
		$deliveryInstance = parent::createInstance($clazz, $label);
		//create a process instance at the same time:
		$processInstance = parent::createInstance(new core_kernel_classes_Class(CLASS_PROCESS), "Process ".$deliveryInstance->getLabel());//the label could be updated
		$deliveryInstance->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT), $processInstance->uriResource);

		return $deliveryInstance;		
	}
	
	 /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneDelivery( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;
		
		$returnValue = $this->createInstance($clazz);
		if(!is_null($returnValue)){
			foreach($clazz->getProperties(true) as $property){
				if($property->uriResource != TAO_DELIVERY_DELIVERYCONTENT){
					//allow clone of every property value but the deliverycontent, which is a process:
					//TODO: cloning a process, idea: using recursive cloning method, i.e. for each prop, if prop is a resource, get the type then clone it again. Idea to be tested
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$returnValue->setPropertyValue($property, $propertyValue);
					}
				}
			}
			$returnValue->setLabel($instance->getLabel()." bis");
		}

        return $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryService */

?>