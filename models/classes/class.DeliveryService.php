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
     * Get all deliveries available for the identified subject.
     * This method is used on the Delivery Server and uses direct access to the database for performance purposes.
	 * It returns an array containing the uri of selected deliveries or an empty array otherwise.
	 * To be tested when core_kernel_impl_ApiModelOO::getObject() is implemented
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string subjectUri
     * @return array
     */
	public function getDeliveriesBySubject($subjectUri){
		
		$returnValue=array();
		
		$groups = core_kernel_impl_ApiModelOO::singleton()->getSubject('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members' , $subjectUri);
		$deliveries = new core_kernel_classes_ContainerCollection(new common_Object());
		foreach ($groups->getIterator() as $group) {
			$deliveries = $deliveries->union(core_kernel_impl_ApiModelOO::singleton()->getObject($group->uriResource, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries'));
		}
		//TODO: eliminate duplicate deliveries (with a function like unique_array() ):
		$returnValue = $deliveries;
		
		
		return $returnValue;
	}
	
	/**
     * The methods getTestStatus checks the value of the property "active" OR "compiled" for a given test instance (a ressource)
     * (Note: For the old implementation of delivery when 1 delivery = 1 test)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource aTestInstance
	 * @param  string status
     * @return boolean
     */
	public function getTestStatus(core_kernel_classes_Resource $aTestInstance, $status){
		
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
				$testId=tao_helpers_Uri::getUniqueId($aTestInstance->uriResource);
				
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
     * add history to a delivery execution
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string deliveryUri
	 * @param  string subjectUri
     * @return core_kernel_classes_ContainerCollection
     */
	public function getHistory(core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $subject = null){
	
		$historyCollection = new core_kernel_classes_ContainerCollection(new common_Object());
		
		if(empty($delivery)){
			throw new Exception("the delivery instance cannot be empty");
		}
		
		if(empty($subject)){
			//select History by delivery only (subject independent listing, i.e. select for all subjects)
			$historyCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_DELIVERY_PROP, $delivery->uriResource);
		}else{
			//select history by delivery and subject
			$historyByDelivery = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_DELIVERY_PROP, $delivery->uriResource);
			$historyBySubject = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_SUBJECT_PROP, $subject->uriResource);
			$historyCollection = $historyByDelivery->intersect($historyBySubject);
		}
		
		return $historyCollection;
	}
	
	/**
     * create a delivery instance, and at the same time the process instance associated to it
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Class clazz
	 * @param  string label
     * @return core_kernel_classes_Resource
     */
	public function createInstance(core_kernel_classes_Class $clazz, $label = ''){
		$deliveryInstance = parent::createInstance($clazz, $label);
		
		//create a process instance at the same time:
		$processInstance = parent::createInstance(new core_kernel_classes_Class(CLASS_PROCESS),'process generated with deliveryService');
		$deliveryInstance->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT), $processInstance->uriResource);
		$this->updateProcessLabel($deliveryInstance);
		
		//set the the default authoring mode to the 'simple mode':
		$deliveryInstance->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP), TAO_DELIVERY_SIMPLEMODE);
		
		//set the default delivery server:
		$defaultResultServer = new core_kernel_classes_Resource(TAO_DELIVERY_DEFAULT_RESULT_SERVER);
		//important to check if it is still there (his property value "url" = localhost)
		$urlResource = $defaultResultServer->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_URL_PROP));
		if(!is_null($urlResource)){
			if($urlResource instanceof core_kernel_classes_Literal){
				if($urlResource->literal == 'localhost'){
					$deliveryInstance->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP), $defaultResultServer->uriResource);
				}
			}
		}
		return $deliveryInstance;		
	}
	
	 /**
     * Clone a delivery instance, and thus its property values, except deliveryContent, which is a process instance, and the compiled property value
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource instance
     * @param  core_kernel_classes_Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneDelivery( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz)
    {
        $returnValue = null;
		
		$clone = $this->createInstance($clazz);
		if(!is_null($clone)){
			foreach($clazz->getProperties(true) as $property){
			
				if($property->uriResource != TAO_DELIVERY_DELIVERYCONTENT && $property->uriResource != TAO_DELIVERY_COMPILED_PROP){
					//allow clone of every property value but the deliverycontent, which is a process:
					//TODO: cloning a process, idea: using recursive cloning method, i.e. for each prop, if prop is a resource, get the type then clone it again. Idea to be tested
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$clone->setPropertyValue($property, $propertyValue);
					}
				}
				
			}
			$clone->setLabel($instance->getLabel()." bis");
			$this->updateProcessLabel($clone);
			$returnValue = $clone;
		}

        return $returnValue;
    }
	
	/**
     * Make sure that the delivery and the associated process have the same label
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
     * @return void
     */
	public function updateProcessLabel(core_kernel_classes_Resource $delivery){
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		$process->setLabel("Process ".$delivery->getLabel());
	}

	/**
	 * Get all the tests composing a delivery
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}	 
	 * @param core_kernel_classes_Resource delivery 
	 * @return array of core_kernel_classes_Resource for each Test instance 
	 */
	public function getRelatedTests(core_kernel_classes_Resource $delivery){
		 $returnValue = array();
	
		if(!is_null($delivery)){
		try{
		 	$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		 	$process = $delivery->getUniquePropertyValue(
				new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT)
			);
			if(!is_null($process)){
				$activities = $authoringService->getActivitiesByProcess($process);
			
				foreach($activities as $activity){
					$test = $authoringService->getTestByActivity($activity);
					if(!is_null($test) && $test instanceof core_kernel_classes_Resource){
						$returnValue[$test->uriResource] = $test;
					}
				}
			}
		}
		catch(Exception $e){}
		}
		return $returnValue;
	}
	
	
	
	
	/**
     * Build a sequential process for the delivery from an array of tests 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
	 * @param  array tests
     * @return boolean
     */
	public function setDeliveryTests(core_kernel_classes_Resource $delivery, $tests){
		
		$returnValue = false;
		
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		// get the current process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//set the required process variables subjectUri and wsdlContract
		$var_subjectUri = $this->getProcessVariable("subjectUri");
		$var_subjectLabel = $this->getProcessVariable("subjectLabel");
		$var_wsdl = $this->getProcessVariable("wsdlContract");
		if(is_null($var_subjectUri) || is_null($var_wsdl) || is_null($var_subjectLabel)){
			throw new Exception('one of the required process variables is missing: "subjectUri", "subjectLabel" and/or "wsdlContract"');
		}else{
			//this section of code is no longer required with the use of actual parameters
			// $processVarProp = new core_kernel_classes_Property(PROPERTY_PROCESS_VARIABLE);
			// $process->removePropertyValues($processVarProp);
			// $process->setPropertyValue($processVarProp, $var_subjectUri->uriResource);
			// $process->setPropertyValue($processVarProp, $var_subjectLabel->uriResource);
			// $process->setPropertyValue($processVarProp, $var_wsdl->uriResource);
			
			//new implementation:
			//create formal param instead, associated to the 3 proc var:
			$subjectUriParam = $authoringService->getFormalParameter('subject');
			if(is_null($subjectUriParam)){
				$subjectUriParam = $authoringService->createFormalParameter('subject', 'processvariable', $var_subjectUri->uriResource, 'subject uri (do not delete)');
			}
			
			$subjectLabelParam = $authoringService->getFormalParameter('subjectLabel');
			if(is_null($subjectLabelParam)){
				$subjectLabelParam = $authoringService->createFormalParameter('subjectlabel', 'processvariable', $var_subjectLabel->uriResource, 'subject label (do not delete)');
			}
			
			$wsdlParam = $authoringService->getFormalParameter('wsdl');
			if(is_null($wsdlParam)){
				$wsdlParam = $authoringService->createFormalParameter('wsdl', 'processvariable', $var_wsdl->uriResource, 'wsdl constract url (do not delete)');
			}
			// var_dump($subjectUriParam, $subjectLabelParam, $wsdlParam);
			//set default process variable:
		}
		
		//delete all related activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		foreach($activities as $activity){
			if(!$authoringService->deleteActivity($activity)){
				return $returnValue;
			}
		}
		
		//create the list of activities and interactive services and tests plus their appropriate property values:
		$totalNumber = count($tests);//0...n
		$previousConnector = null; 
		for($i=0;$i<$totalNumber;$i++){
			$test = $tests[$i];
			if(!($test instanceof core_kernel_classes_Resource)){
				throw new Exception("the array element n$i is not a Resource");
			}
			
			//create an activity
			$activity = null;
			$activity = $authoringService->createActivity($process, "test: {$test->getLabel()}");
			if($i==0){
				//set the property value as initial
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
			}
			
			//set property value visible to true
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), GENERIS_FALSE);
			
			//get the service definition with the wanted test uri (if doesn't exist, create one)
			// $testId = tao_helpers_Uri::getUniqueId($test->uriResource);
			// $testUrl = BASE_URL."/compiled/{$testId}/theTest.php?subject=^subjectUri&subjectLabel=^subjectLabel&wsdl=^wsdlContract";
			
			$testUrl = tao_helpers_Precompilator::getCompiledTestUrl($test->uriResource);
			
			$serviceDefinition = null;
			$serviceDefinitionCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_SUPPORTSERVICES_URL,$testUrl);
			if(!$serviceDefinitionCollection->isEmpty()){
				if($serviceDefinitionCollection->get(0) instanceof core_kernel_classes_Resource){
					$serviceDefinition = $serviceDefinitionCollection->get(0);
				}
			}
			if(is_null($serviceDefinition)){
				//if no corresponding service def found, create a service definition:
				$serviceDefinitionClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
				$serviceDefinition = $serviceDefinitionClass->createInstance($test->getLabel(), 'created by delivery service');
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL), $testUrl);
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $subjectUriParam->uriResource);
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $subjectLabelParam->uriResource);
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $wsdlParam->uriResource);
				
			}
			//create a call of service and associate the service definition to it:
			$interactiveService = $authoringService->createInteractiveService($activity);
			$interactiveService->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
			$authoringService->setActualParameter($interactiveService, $subjectUriParam, $var_subjectUri->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMIN, PROPERTY_ACTUALPARAM_PROCESSVARIABLE);
			$authoringService->setActualParameter($interactiveService, $subjectLabelParam, $var_subjectLabel->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMIN, PROPERTY_ACTUALPARAM_PROCESSVARIABLE);
			$authoringService->setActualParameter($interactiveService, $wsdlParam, $var_wsdl->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMIN, PROPERTY_ACTUALPARAM_PROCESSVARIABLE);
				
			if($totalNumber == 1){
				if(!is_null($interactiveService) && $interactiveService instanceof core_kernel_classes_Resource){
					return true;
				}
			}
			if($i<$totalNumber-1){
				//get the connector created as the same time as the activity and set the type to "sequential" and the next activity as the selected service definition:
				$connector = $authoringService->createConnector($activity);
				if(!($connector instanceof core_kernel_classes_Resource) || is_null($connector)){
					throw new Exception("the created connector is not a resource");
					return $returnValue;
				}
			
				$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
				
				if(!is_null($previousConnector)){
					$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $activity->uriResource);
				}
				$previousConnector = $connector;//set the current connector as "the previous one" for the next loop	
			}
			else{
				//if it is the last test of the array, no need to add a connector: just connect the previous connector to the last activity
				$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $activity->uriResource);
				//every action is performed:
				$returnValue = true;
			}
		}
		
		return $returnValue;
	}
	
	/**
     * Get an ordered array of tests that make up a sequential process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
     * @return array
     */
	public function getDeliveryTests(core_kernel_classes_Resource $delivery){
		
		$tests = array();
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		//get the associated process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		$totalNumber = count($activities);
		
		//find the first one: property isinitial == true (must be only one, if not error) and set as the currentActivity:
		$currentActivity = null;
		foreach($activities as $activity){
			
			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->uriResource == GENERIS_TRUE){
					$currentActivity = $activity;
					break;
				}
			}
		}
		
		if(is_null($currentActivity)){
			return $tests;
		}
		
		//start the loop:
		for($i=0;$i<$totalNumber;$i++){
			//get the FIRST interactive service
			$iService = $currentActivity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			if(is_null($iService)){
				throw new Exception('the current activity has no interactive service');
			}
			//get the service definition
			$serviceDefinition = $iService->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
			
			//get the url
			$serviceUrl = $serviceDefinition->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL));

			//regenerated the test uri
			$testUri = tao_helpers_Precompilator::getTestUri($serviceUrl);
			
			//set the test in the table:
			$tests[$i] = new core_kernel_classes_Resource($testUri);
			
			
			//get its connector (check the type is "sequential) if ok, get the next activity
			$connectorCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $currentActivity->uriResource);
			$nextActivity = null;
			foreach($connectorCollection->getIterator() as $connector){
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				if($connectorType->uriResource = INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
					$nextActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
					break;
				}
			}
			if(!is_null($nextActivity)){
				$currentActivity = $nextActivity;
			}else{
				if($i == $totalNumber-1){
					//it is normal, since it is the last activity and test
				}else{
					throw new Exception('the next activity of the connector is not found');
				}	
			}
		}
		
		//final check:
		
		return $tests;
	}
	
	/**
     * Short description of method getDeliveriesTests
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getDeliveriesTests()
    {
        $returnValue = array();

        // section 127-0-1-1-35b227b4:127a93c45f1:-8000:0000000000002346 begin
        foreach($this->deliveryClass->getInstances(true) as $delivery){
        	$returnValue[$delivery->uriResource] =  $this->getRelatedTests($delivery);
        }
        
        // section 127-0-1-1-35b227b4:127a93c45f1:-8000:0000000000002346 end

        return (array) $returnValue;
    }
	
	/**
     * Get all available tests
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return array
     */
	public function getAllTests(){
		
		$returnValue = array();
		
		$testClazz = new core_kernel_classes_Class(TAO_TEST_CLASS);
		foreach($testClazz->getInstances(true) as $instance){
			$returnValue[$instance->uriResource] = $instance->getLabel();
		}
		
		return $returnValue;
	}
	
	/**
     * Get the process variable with a given code
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
     * @return boolean
     */
	public function linearizeDeliveryProcess(core_kernel_classes_Resource $delivery){
		
		$returnValue = false;
		
		//get list of all tests in the delivery, without order:
		$tests = array();
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		//get the associated process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		
		foreach($activities as $activity){
			
			//get the FIRST interactive service
			$iService = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			if(!is_null($iService)){
				
				//get the service definition
				$serviceDefinition = $iService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
				if(!is_null($serviceDefinition)){
					
					//get the url
					$serviceUrl = $serviceDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL));
					if(!is_null($serviceUrl)){
					
						//regenerate the test uri
						$testUri = tao_helpers_Precompilator::getTestUri($serviceUrl);
						if(!empty($testUri)){
							//set the test in the table:
							$tests[$testUri] = new core_kernel_classes_Resource($testUri);
						}
						
					}
					
				}
				
			}
			
		}
		//the functuon setDeliveryTests require an array with numerical key 
		$numericalKeyTestArray = array();
		foreach($tests as $test){
			$numericalKeyTestArray[] = $test;
		}
		
		$returnValue = $this->setDeliveryTests($delivery, $numericalKeyTestArray);
		return $returnValue;
	}
	
	/**
     * Get the process variable with a given code
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string code
     * @return core_kernel_classes_Resource or null
     */
	public function getProcessVariable($code){
		$procVar = null;
		
		$varCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE, $code);
		if(!$varCollection->isEmpty()){
			if($varCollection->get(0) instanceof core_kernel_classes_Resource){
			$procVar = $varCollection->get(0);
			}
		}
		
		return $procVar;
	}
	
	/**
     * The the url of the select result server
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return string
     */
	public function getResultServer(core_kernel_classes_Resource $delivery){
		
		$returnValue='';
		
		if(!is_null($delivery)){
		
			$resultServer = $delivery->getOnePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer"));
			if(!is_null($resultServer) && $resultServer instanceof core_kernel_classes_Resource){
				//potential issue with the use of common_Utils::isUri in getPropertyValuesCollection() or store encoded url only in
				$resultServerUrl = $resultServer->getOnePropertyValue(new core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl"));
				if(!is_null($resultServerUrl)){
					$wsdlContractPath = '';
					if($resultServerUrl instanceof core_kernel_classes_Literal){
						$wsdlContractPath = $resultServerUrl->literal;
					}
					if($resultServerUrl instanceof core_kernel_classes_Resource){
						$wsdlContractPath = $resultServerUrl->uriResource;
					}
					if(!empty($wsdlContractPath)){
						if(strtolower($wsdlContractPath) == 'localhost'){
							//if "localhost" is specified, use the default local result server
							$returnValue = LOCAL_RESULT_SERVER;//check value on the config.php file
						}else{
							//TODO verify more thoroughly the validity of the wsdl contract
							// if(file_exists($wsdlContractPath)){//use is_readable?
								// $returnValue = $wsdlContractPath;
							// }
							$returnValue = $wsdlContractPath;
						}
					}
				}
			}
			
		}
		
		return $returnValue;
	}

} /* end of class taoDelivery_models_classes_DeliveryService */

?>