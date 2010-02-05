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
 * The taoDelivery_models_classes_ProcessAuthoringService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_ProcessAuthoringService
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
	
	protected $activityClass = null;
	protected $roleClass = null;
	protected $serviceDefinitionClass = null;
	protected $formalParameterClass = null;
	
	protected $processUri = '';
		
    /**
     * The attribute deliveryOntologies contains the reference to the TAODelivery Ontology
     *
     * @access protected
     * @var array
     */
    protected $processOntologies = array(
		'http://www.tao.lu/Ontologies/TAODelivery.rdf',
		'http://www.tao.lu/Ontologies/TAOTest.rdf',
		'http://www.tao.lu/middleware/hyperclass.rdf',
		'http://www.tao.lu/middleware/taoqual.rdf',
		'http://www.tao.lu/middleware/Rules.rdf',
		'http://www.tao.lu/middleware/Interview.rdf'
		);
			
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
		$this->activityClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
		$this->roleClass = new core_kernel_classes_Class(CLASS_ROLE);
		$this->serviceDefinitionClass = new core_kernel_classes_Class(CLASS_SERVICESDEFINITION);
		$this->formalParameterClass = new core_kernel_classes_Class(CLASS_FORMALPARAMETER);
		
		//set processUri here
		
		$this->loadOntologies($this->processOntologies);
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
	 //UL
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
	 //UL
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
    public function getInstance($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

		if(is_null($clazz) || !$this->isAuthorizedClass($clazz)){
			return $returnValue;
		}
		$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
				
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
	 //UL
    // public function createDeliveryClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    // {
        // $returnValue = null;

		// if(is_null($clazz)){
			// $clazz = $this->deliveryClass;
		// }
		
		// if($this->isDeliveryClass($clazz)){
		
			// $deliveryClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			// foreach($properties as $propertyName => $propertyValue){
				// $myProperty = $deliveryClass->createProperty(
					// $propertyName,
					// $propertyName . ' ' . $label .' delivery property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				// );
			// }
			// $returnValue = $deliveryClass;
		// }

        // return $returnValue;
    // }
	
	/**
     * Method to be called to delete an instance
     * (Method is not used in the current implementation yet)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource instance
     * @return boolean
     */
    public function deleteInstance( core_kernel_classes_Resource $instance)
    {
        $returnValue = (bool) false;
		
		if(!is_null($instance)){
			$returnValue = $instance->delete();
		}

        return (bool) $returnValue;
    }
	
	public function createInteractiveService(core_kernel_classes_Resource $activity){
		//retrouver systematiquement l'actual parameter associé à chaque fois, à partir du formal parameter et call of service, lors de la sauvegarde
		//an interactive service of an activity is a call of service:
		$callOfServiceClass = new core_kernel_classes_Class(CLASS_CALLOFERVICES);
		
		//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT
		$callOfService = $callOfServiceClass->createInstance($formalParam->getLabel(), "created by DeliveryAuthoring.Class");
	}

	public function setActualParameter(core_kernel_classes_Resource $callOfService, core_kernel_classes_Resource $formalParam, $value, $parameterInOrOut, $actualParameterType=''){
		
		//to be clarified:
		$actualParameterType = PROPERTY_ACTUALPARAM_PROCESSVARIABLE; //PROPERTY_ACTUALPARAM_CONSTANTVALUE;//PROPERTY_ACTUALPARAM_PROCESSVARIABLE //PROPERTY_ACTUALPARAM_QUALITYMETRIC
		
		//retrouver systematiquement l'actual parameter associé à chaque fois, à partir du formal parameter et call of service, lors de la sauvegarde
		$actualParameterClass = new core_kernel_classes_Class(CLASS_ACTUALPARAMETER);
		
		//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT
		$newActualParameter = $actualParameterClass->createInstance($formalParam->getLabel(), "created by DeliveryAuthoring.Class");
		$newActualParameter->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_FORMALPARAM), $formalParam->uriResource);
		$newActualParameter->setPropertyValue(new core_kernel_classes_Property($actualParameterType), $value);
	
		$callOfService->setPropertyValue(new core_kernel_classes_Property($parameterInOrOut), $formalParam->uriResource);
	}
	
	//clean the triples for a call of service and its related resource (i.e. actual parameters)
	public function deleteActualParameters(core_kernel_classes_Resource $callOfService){
		
		$returnValue = (bool) false;
		
		if(is_null($callOfService) || !($callOfService instanceof core_kernel_classes_Resource)){
			throw new Exception("no valid Call of Service in function parameter");
			return $returnValue;
		}
		
		//get all actual param of the current call of service
		$actualParamCollection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN));
		$actualParamCollection = $actualParamCollection->union($callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT)));
		
		//delete all of them:
		foreach($actualParamCollection->getIterator() as $actualParam){
		
			if($actualParam instanceof core_kernel_classes_Resource){
				$returnValue=$actualParam->delete();
				if(!$returnValue) {
					return (bool) $returnValue;
				}
			}
		}
		
		//remove the property values in the call of service instance
		$returnValue = $callOfService->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMIN));
		$returnValue = $callOfService->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT));
		
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
	 //UL
    // public function deleteDeliveryClass( core_kernel_classes_Class $clazz)
    // {
        // $returnValue = (bool) false;

		// if(!is_null($clazz)){
			// if($this->isDeliveryClass($clazz) && $clazz->uriResource != $this->deliveryClass->uriResource){
				// $returnValue = $clazz->delete();
			// }
		// }

        // return (bool) $returnValue;
    // }

    /**
     * Check whether the object is a delivery class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function isAuthorizedClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		$authorizedClassUri=array(
			CLASS_ACTIVITIES,
			CLASS_SERVICESDEFINITION,
			CLASS_WEBSERVICES,
			CLASS_SUPPORTSERVICES,
			CLASS_FORMALPARAMETER,
			CLASS_ROLE
		);
		
		if( in_array($clazz->uriResource, $authorizedClassUri) ){
			$returnValue = true;	
		}
		//unquote and edit the block below only if subclass for workflow process definition is authorized
		// else{
			// foreach($this->everyAuthorizeClass->getSubClasses(true) as $subclass){
				// if($clazz->uriResource == $subclass->uriResource){
					// $returnValue = true;
					// break;	
				// }
			// }
		// }

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
	
	public function createActivity(){
	
	}
	
	public function getActivitiesByProcess($processUri = ''){
		
		$returnValue = array();
		
		//eventually, put $processUri in a class property
		if(empty($processUri)){
			$processUri = $this->processUri;
		}
		if(empty($processUri)){
			throw new Exception("no process Uri found");
			return $returnValue;
		}
		
		$process = new core_kernel_classes_Resource($processUri);
		foreach ($process->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Resource ){
				$returnValue[] = $value;
			}
		}
		
		return $returnValue;
	}
	
	public function getActivityConnectors($activityUri, $option=array() ){
			
		//prev: the connectors that links to the current activity
		//next: the connector (should be unique for an activiy that is not a connector itself) that follow the current activity
		$returnValue = array(
			'prev'=>array(),
			'next'=>array()
		);
		
		if(empty($option)){
		//the default option: select all connectors
			$option = array('prev','next');
		}else{
			$option = array_map('strtolower', $option);
		}
		
		if(in_array('prev',$option)){
		
			$previousConnectorsCollection=core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activityUri);
		
			foreach ($previousConnectorsCollection->getIterator() as $connector){
				if(!is_null($connector)){
					if($connector instanceof core_kernel_classes_Resource ){
						$returnValue['prev'][] = $connector; 
					}
				}
			}
		}
		
		if(in_array('next',$option)){
		
			$followingConnectorsCollection=core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES, $activityUri);
		
			foreach ($followingConnectorsCollection->getIterator() as $connector){
				if(!is_null($connector)){
					if($connector instanceof core_kernel_classes_Resource ){
						$returnValue['next'][] = $connector; 
						//break; //select the unique FOLLOWING connector in case of a real activity  (i.e. not a connector)
					}
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
			}
			
		}
		
		return $returnValue;
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
	public function getHistory($deliveryUri, $subjectUri=""){
	
		$historyCollection = null;
		
		if(empty($deliveryUri)){
			throw new Exception("the delivery uri cannot be empty");
		}
		if(empty($subjectUri)){
			//select History by delivery only (subject independent listing, i.e. select for all subjects)
			$historyCollection=core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_DELIVERY_PROP, $deliveryUri);
		}else{
			
			$validSubjectUri=true;//TODO check if it is a valid subject
			if($validSubjectUri){
				//select history by delivery and subject
				$historyByDelivery=core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_DELIVERY_PROP, $deliveryUri);
				$historyBySubject=core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_SUBJECT_PROP, $subjectUri);
				$historyCollection=$historyByDelivery->intersect($historyBySubject);
			}else{
				throw new Exception("invalid subject uri");
			}
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
		// if(empty($subjectUri)) throw new Exception("the subject uri cannot be empty");
		// if(empty($deliveryUri)) throw new Exception("the delivery uri cannot be empty");
		
		$history = $this->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_HISTORY_CLASS));
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP), $subjectUri);
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_DELIVERY_PROP), $deliveryUri);
		$history->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP), time() );
	}
	

} /* end of class taoDelivery_models_classes_DeliveryService */

?>