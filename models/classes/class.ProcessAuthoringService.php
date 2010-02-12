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
		$number = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->count();
		$number += 1;
		
		//an interactive service of an activity is a call of service:
		$callOfServiceClass = new core_kernel_classes_Class(CLASS_CALLOFSERVICES);
		
		//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT
		$callOfService = $callOfServiceClass->createInstance("InteractiveService_$number", "created by ProcessAuthoringService.Class");
		
		if(!empty($callOfService)){
			//associate the new instance to the activity instance
			$activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $callOfService->uriResource);
		}else{
			throw new Exception("the interactive service cannot be created for the activity {$activity->uriResource}");
		}
		
		return $callOfService;
	}

	public function setActualParameter(core_kernel_classes_Resource $callOfService, core_kernel_classes_Resource $formalParam, $value, $parameterInOrOut, $actualParameterType=''){
		
		//to be clarified:
		$actualParameterType = PROPERTY_ACTUALPARAM_PROCESSVARIABLE; //PROPERTY_ACTUALPARAM_CONSTANTVALUE;//PROPERTY_ACTUALPARAM_PROCESSVARIABLE //PROPERTY_ACTUALPARAM_QUALITYMETRIC
		
		//retrouver systematiquement l'actual parameter associé à chaque fois, à partir du formal parameter et call of service, lors de la sauvegarde
		$actualParameterClass = new core_kernel_classes_Class(CLASS_ACTUALPARAMETER);
		
		//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT
		$newActualParameter = $actualParameterClass->createInstance($formalParam->getLabel(), "created by Process Authoring Service");
		$newActualParameter->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_FORMALPARAM), $formalParam->uriResource);
		$newActualParameter->setPropertyValue(new core_kernel_classes_Property($actualParameterType), $value);
	
		return $callOfService->setPropertyValue(new core_kernel_classes_Property($parameterInOrOut), $newActualParameter->uriResource);
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
		if($actualParamCollection->count()<=0){
			return true;//no need to delete anything
		}
		
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
		$callOfService->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMIN));
		$callOfService->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT));
		
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
			CLASS_ROLE,
			CLASS_PROCESS
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
	
	public function createActivity(core_kernel_classes_Resource $process, $label=''){
		
		$activityLabel = "";
		if(empty($label)){
			$number = $process->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES))->count();
			$number += 1;
			$activityLabel = "Activity_$number";
		}else{
			$activityLabel = $label;
		}
		
		$activityClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
		$activity = $activityClass->createInstance($activityLabel, "created by ProcessAuthoringService.Class");
		
		if(!empty($activity)){
			//associate the new instance to the process instance
			$process->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activity->uriResource);
		}else{
			throw new Exception("the activity cannot be created for the process {$process->uriResource}");
		}
		return $activity;
	}
	
	public function getActivitiesByProcess($processUri = ''){
		
		$returnValue = array();
		
		//eventually, put $processUri in a class property
		if(empty($processUri) && !empty($this->processUri)){
			$processUri = $this->processUri;
		}
		if(empty($processUri)){
			throw new Exception("no process Uri found");
			return $returnValue;
		}
		
		$process = new core_kernel_classes_Resource($processUri);
		foreach ($process->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES))->getIterator() as $activity){
			if($activity instanceof core_kernel_classes_Resource){
				$returnValue[$activity->uriResource] = $activity;
			}
		}
		
		return $returnValue;
	}
	
	public function getConnectorsByProcess($processUri = ''){
		$activities = $this->getActivitiesByProcess($processUri);
		$connectors = array();
		foreach($activities as $activity){
			$tempConnectorArray = array();
			$tempConnectorArray = $this->getConnectorsByActivity($activity->uriResource, array('next'));
		}
	
	}
	
	public function getConnectorsByActivity($activityUri, $option=array(), $isConnector=false ){
			
		//prev: the connectors that links to the current activity
		//next: the connector (should be unique for an activiy that is not a connector itself) that follows the current activity
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
						$returnValue['prev'][$connector->uriResource] = $connector; 
					}
				}
			}
		}
		
		if(in_array('next',$option)){
		
			$followingConnectorsCollection=core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES, $activityUri);
		
			foreach ($followingConnectorsCollection->getIterator() as $connector){
				if(!is_null($connector)){
					if($connector instanceof core_kernel_classes_Resource){
						$returnValue['next'][$connector->uriResource] = $connector; 
						if($isConnector){
							continue; //continue selecting potential other following activities or connector
						}else{
							break; //select the unique FOLLOWING connector in case of a real activity  (i.e. not a connector)
						}
					}
				}
			}
		}
		
		return $returnValue;
	}
	
	public function activityTree(core_kernel_classes_Resource $process = null){
		
		$data = array();
		
		if(empty($process) && !empty($this->processUri)){
			$process = new core_kernel_classes_Resource($this->processUri);
		}
		if(empty($process)){
			throw new Exception("no process instance to populate the activity tree");
			return $data;
		}
		
		//initiate the return data value:
		$data = array(
			'data' => __("Process Activities"),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($process->uriResource),
				'class' => 'node-root'
			),
			'children' => array()
		);
	
		$activities = $this->getActivitiesByProcess($process->uriResource);
		
		foreach($activities as $activity){
			
			$activityData = array(
				'data' => $activity->getLabel(),
				'attributes' => array(
					'id' => tao_helpers_Uri::encode($activity->uriResource),
					'class' => 'node-activity'
				),
				'children' => array()
			);
			
			//set property node:
			$activityData['children'][] = array(
				'data' => __("Property"),
				'attributes' => array(
					'id' => "prop_".tao_helpers_Uri::encode($activity->uriResource),
					'class' => 'node-property'
				)
			);
			
			
			
			//get connectors
			$connectors = $this->getConnectorsByActivity($activity->uriResource);
			
			
			if(!empty($connectors['prev'])){
				//activity connected to a previous one:
				foreach($connectors['prev'] as $connector){
				
					$connectorData = array();
					
					//type of connector:
					$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
					
					//if it is a split type
					if( strtolower($connectorType->getLabel()) == "split"){
						//get the rule
						$connectorRule = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
						$connectorData[] = array(
							'data' => $connectorRule->getLabel(),
							'attributes' => array(
								'rel' => tao_helpers_Uri::encode($connectorRule->uriResource),
								'class' => 'node-rule'
							)
						);
						
						//get the "PREC"
						$prev = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
						/*//is it a connector or an activity??
						$activityType = core_kernel_classes_ApiModelOO::singleton()->getObject($prev->uriResource, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
						if( $activityType == CLASS_ACTIVITIES){//use static method isActivity($prev) instead
							//it is an activity:
							$nodeClass = 'node-activity-goto';
						}elseif($activityType == CLASS_CONNECTORS){
							$nodeClass = 'node-connector-goto';
						}else{
							continue;//unknown type...
						}
						
						$connectorData[] = array(
							'data' => $prev->getLabel(),
							'attributes' => array(
								'rel' => tao_helpers_Uri::encode($prev->uriResource),
								'class' => $nodeClass
							)
						);*/
						$connectorData[] = $this->activityNode($prev, 'prec', true);
												
					}elseif(strtolower($connectorType->getLabel()) == "sequence"){
						$prev = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
						if(!self::isActivity($prev)){
							throw new Exception("the previous activity of a sequence connector must be an activity");
						}
						$connectorData[] = $this->activityNode($prev, 'next', true);
						
						// $connectorData[] = array(
							// 'data' => $prev->getLabel(),
							// 'attributes' => array(
								// 'rel' => tao_helpers_Uri::encode($prev->uriResource),
								// 'class' => 'node-activity-goto'
							// )
						// );
					}
					
					//add to activity data
					$activityData[] = array(
						'data' => $connectorType->getLabel().":".$connector->getLabel(),
						'attributes' => array(
							'rel' => tao_helpers_Uri::encode($connector->uriResource),
							'class' => 'node-connector-prev'
						),
						'children' => $connectorData
					);
				}
				
			}
			
			//following nodes:
			if(!empty($connectors['next'])){
				//connector following the current activity: there should be only one
				foreach($connectors['next'] as $connector){
				
					$this->connectorNode($connector, true);
					$connectorData = array();
					
					//type of connector:
					$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
					//if it is a split type
					if( strtolower($connectorType->getLabel()) == "split"){
						//get the rule
						$connectorRule = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
						$connectorData[] = array(
							'data' => "IF ".$connectorRule->getLabel(),
							'attributes' => array(
								'id' => tao_helpers_Uri::encode($connectorRule->uriResource),
								'class' => 'node-rule'
							)
						);
						
						//get the "THEN"
						$then = $connectorRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULE_THEN));
						//is it a connector or an activity??
						$activityType = core_kernel_classes_ApiModelOO::singleton()->getObject($then->uriResource, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
						if( $activityType == CLASS_ACTIVITIES){
							//it is an activity:
							$nodeClass = 'node-activity-goto';
						}elseif($activityType == CLASS_CONNECTORS){
							$nodeClass = 'node-connector-goto';
						}else{
							continue;//unknown type...
						}
						$connectorData[] = array(
							'data' => __('then').' '.$then->getLabel(),
							'attributes' => array(
								'rel' => tao_helpers_Uri::encode($then->uriResource),
								'class' => $nodeClass
							)
						);
						
						//compare if the current connector is created from the current activity or is simply a link to another one:
						$connectorActivityReference = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->literal;
						$goto = true;
						if ($connectorActivityReference == $activity->uriResource){
							$goto = false;
							//recursive node building with activity
						}
						$connectorData[] = $this->activityNode($then, 'then', $goto);
						
						//get the "ELSE"
						$else = $connectorRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULE_ELSE));
						//is it a connector or an activity??
						$activityType = core_kernel_classes_ApiModelOO::singleton()->getObject($else->uriResource, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
						if( $activityType == CLASS_ACTIVITIES){
							//it is an activity:
							$nodeClass = 'node-activity-goto';
						}elseif($activityType == CLASS_CONNECTORS){
							$nodeClass = 'node-connector-goto';
						}else{
							continue;//unknown type...
						}
						$connectorData[] = array(
							'data' => __('else').' '.$else->getLabel(),
							'attributes' => array(
								'rel' => tao_helpers_Uri::encode($else->uriResource),
								'class' => $nodeClass
							)
						);
					}elseif(strtolower($connectorType->getLabel()) == "sequence"){
						$next = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
						
						$connectorData[] = array(
							'data' => $next->getLabel(),
							'attributes' => array(
								'rel' => tao_helpers_Uri::encode($next->uriResource),
								'class' => 'node-activity-goto'
							)
						);
					}
					
					//add to data
					$activityData['children'][] = array(
						'data' => $connectorType->getLabel().":".$connector->getLabel(),
						'attributes' => array(
							'id' => tao_helpers_Uri::encode($connector->uriResource),
							'class' => 'node-connector-next'
						),
						'children' => $connectorData
					);
					
				}
			}
			
			//get iservices
			$services = null;
			$services = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			foreach($services->getIterator() as $service){
				if($service instanceof core_kernel_classes_Resource){
					$activityData['children'][] = array(
						'data' => $service->getLabel(),
						'attributes' => array(
							'id' => tao_helpers_Uri::encode($service->uriResource),
							'class' => 'node-interactive-service'
						)
					);
				}
			}
			
			//get related rules
			
			
			
			$data["children"][] = $activityData;
		}
		return $data;
	}
	
	public function connectorNode($connector, $recursive=false){//put the current activity as a protected property of the class Process aythoring Tree
		// $this->connectorNode($connector, true); call of function
		
		$returnValue = array();
		$connectorData = array();
					
		//type of connector:
		$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
					
		//if it is a split type
		if( strtolower($connectorType->getLabel()) == "split"){
			//get the rule
			$connectorRule = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
			$connectorData[] = $this->ruleNode($connectorRule);
			
			//get the "THEN"
			$then = $connectorRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULE_THEN));
			$connectorActivityReference = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->literal;
			if(self::isConnector($then) && ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($else->uriResource, $this->addedConnectors)){
				if($recursive){
					$connectorData[] = $this->connectorNode($then, true);
				}else{
					$connectorData[] = $this->activityNode($then, 'then', false);
				}
			}else{
				$connectorData[] = $this->activityNode($then, 'then', true);
			}
			
			//same for the "ELSE"
			$else = $connectorRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULE_ELSE));
			$connectorActivityReference = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->literal;
			if(self::isConnector($else) && ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($else->uriResource, $this->addedConnectors)){
				if($recursive){
					$connectorData[] = $this->connectorNode($else, true);
				}else{
					$connectorData[] = $this->activityNode($else, 'else', false);
				}
			}else{
				$connectorData[] = $this->activityNode($else, 'else', true);
			}
		}elseif(strtolower($connectorType->getLabel()) == "sequence"){
			$next = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
			$connectorData[] = $this->activityNode($next, 'next', true);
		}else{
			throw new Exception("unknown connector type");
		}
					
		//add to data
		$returnValue = array(
			'data' => $connectorType->getLabel().":".$connector->getLabel(),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($connector->uriResource),
				'class' => 'node-connector-next'
			),
			'children' => $connectorData
		);
		
		return $returnValue;
	}
						
	public function ruleNode(core_kernel_classes_Resource $rule){
		$labelPrefix = __("if").' ';
		
		$nodeData = array(
			'data' => $labelPrefix.$rule->getLabel(),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($rule->uriResource),
				'class' => 'node-rule'
			)
		);
		
		return $nodeData;
	}

	
	public function activityNode(core_kernel_classes_Resource $activity, $nodeClass='', $goto=false){
		$nodeData = array();
		$class = '';
		$linkAttribute = 'id';
		
		if(self::isActivity($activity)){
			$class = 'node-activity';
		}elseif(self::isConnector($activity)){
			$class = 'node-connector';
			
		}else{
			return $nodeData;//unknown type
		}
		
		if($goto){
			$class .= "-goto";
			$linkAttribute = "rel";
		}
		
		$labelPrefix = '';
		switch(strtolower($nodeClass)){
			case 'prev':
				break;
			case 'next':
				break;
			case 'then':
				$labelPrefix = __('then').' ';
				break;
			case 'else':
				$labelPrefix = __('else').' ';
				break;
		}
		
		$nodeData = array(
			'data' => $labelPrefix.$activity->getLabel(),
			'attributes' => array(
				$linkAttribute => tao_helpers_Uri::encode($activity->uriResource),
				'class' => $class
			)
		);
		
		return $nodeData;
	}
	
	public static function isActivity(core_kernel_classes_Resource $resource){
		$returnValue = false;
		
		$activityType = core_kernel_classes_ApiModelOO::singleton()->getObject($resource->uriResource, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
		if($activityType->count()>0){
			if($activityType->get(0) instanceof core_kernel_classes_Resource){//should be a generis class
				if( $activityType->get(0)->uriResource == CLASS_ACTIVITIES){
					$returnValue = true;
				}
			}
		}
		
		return $returnValue;
	}
	
	public static function isConnector(core_kernel_classes_Resource $resource){
		$returnValue = false;
		
		$activityType = core_kernel_classes_ApiModelOO::singleton()->getObject($resource->uriResource, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type')->literal;
		if($activityType->count()>0){
			if($activityType->get(0) instanceof core_kernel_classes_Resource){
				if( $activityType->get(0)->uriResource == CLASS_CONNECTORS){
					$returnValue = true;
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