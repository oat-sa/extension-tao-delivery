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
 * 
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('taoDelivery/plugins/CapiXML/models/class.ConditionalTokenizer.php');

/**
 * 
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('taoDelivery/plugins/CapiImport/models/class.DescriptorFactory.php');

/**
 * The taoDelivery_models_classes_ProcessAuthoringService class provides methods to access and edit the process ontology
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
	
	protected $processUri = '';
		
    /**
     * The attribute deliveryOntologies contains the reference to the required Ontologies
     *
     * @access protected
     * @var array
     */
    protected $processAuthoringOntologies = array(
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
		
		//set processUri here
		
		$this->loadOntologies($this->processAuthoringOntologies);
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
    public function getInstance($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null){
        $returnValue = null;

		if(is_null($clazz) || !$this->isAuthorizedClass($clazz)){
			return $returnValue;
		}
		$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
				
        return $returnValue;
    }
		
	/**
     * Method to be called to delete an instance
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource instance
     * @return boolean
     */
    public function deleteInstance( core_kernel_classes_Resource $instance){
        $returnValue = (bool) false;
		
		if(!is_null($instance)){
			$returnValue = $instance->delete();
		}

        return (bool) $returnValue;
    }
	
	/**
     * Description
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
     * @return core_kernel_classes_Resource
     */
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
	
	/**
     * Description
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
	 * @param  core_kernel_classes_Resource formalParam
	 * @param  string value
	 * @param  string parameterInOrOut
	 * @param  string actualParameterType
     * @return boolean
     */
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
	
	/**
     * Clean the triples for a call of service and its related resource (i.e. actual parameters)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource callOfService
     * @return boolean
     */
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
     * Clean the triples for a rule and its related resource
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource rule
     * @return boolean
     */
	public function deleteRule(core_kernel_classes_Resource $rule){
		$returnValue = false;
		
		//get the rule type:
		if(!is_null($rule)){
			//if it is a transition rule: get the uri of the related properties: THEN and ELSE:
			//delete the expression of the conditio and its related terms
			$expressionCollection = $rule->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			foreach($expressionCollection->getIterator() as $expression){
				$this->deleteExpression($expression);
			}
			
			//delete the resources
			$returnValue = $rule->delete();
		}
		
		return $returnValue;
	}
	
	/**
     * Clean the triples for an expression and its related resource
	 * note: always recursive: delete the expressions that make up the current expression
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource expression
     * @return boolean
     */
	public function deleteExpression(core_kernel_classes_Resource $expression){
		
		$returnValue = false;
		
		//delete related expressions
		$firstExpressionCollection = $expression->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_EXPRESSION_FIRSTEXPRESSION));
		$secondExpressionCollection = $expression->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_EXPRESSION_SECONDEXPRESSION));
		$expressionCollection = $firstExpressionCollection->union($secondExpressionCollection);
		foreach($expressionCollection->getIterator() as $exp){
				$this->deleteExpression($exp);
		}
		
		$terminalExpression = $expression->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_EXPRESSION_TERMINALEXPRESSION));
		if(!empty($terminalExpression) && $terminalExpression instanceof core_kernel_classes_Resource){
			$terminalExpression->delete();
		}
		
		//delete the expression itself:
		$returnValue = $expression->delete();
		
		return $returnValue;
	}
	
	/**
     * Clean the ontology from a process triples
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource process
     * @return boolean
     */
	public function deleteProcess(core_kernel_classes_Resource $process){
		
		$returnValue = false;
		
		if(!is_null($process)){
			$activities = $this->getActivitiesByProcess($process);
			foreach($activities as $activity){
				if(!$this->deleteActivity($activity)){
					return $returnValue;
				}
			}
			
			$returnValue = $process->delete();
		}
		
		return $returnValue;
	}
	
	/**
     * Clean the ontology from an activity triples
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
     * @return boolean
     */
	public function deleteActivity(core_kernel_classes_Resource $activity){
		
		$returnValue = false;
		
		$apiModel = core_kernel_classes_ApiModelOO::singleton();
		
		//delete the activity reference in the process instance.
		$processCollection = $apiModel->getSubject(PROPERTY_PROCESS_ACTIVITIES , $activity->uriResource);
		if(!$processCollection->isEmpty()){
			$apiModel->removeStatement($processCollection->get(0)->uriResource, PROPERTY_PROCESS_ACTIVITIES, $activity->uriResource, '');
		}else{
			return false;
		}
		
		//delete related connector
		$connectorCollection = $apiModel->getSubject(PROPERTY_CONNECTORS_ACTIVITYREFERENCE , $activity->uriResource);
		foreach($connectorCollection->getIterator() as $connector){
			$this->deleteConnector($connector);
		}
		
		//delete reference to this activity from previous ones, via connectors
		$prevConnectorCollection = $apiModel->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES , $activity->uriResource);
		foreach($prevConnectorCollection->getIterator() as $prevConnector){
			$apiModel->removeStatement($prevConnector->uriResource, PROPERTY_CONNECTORS_NEXTACTIVITIES, $activity->uriResource, '');
			
			/*
			//cleaner method to delete all the reference but much slower
			//get the type of connector is "split", delete the reference in the transition rule: either PROPERTY_TRANSITIONRULES_THEN or ELSE
			if($prevConnector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->uriResource == INSTANCE_TYPEOFCONNECTORS_SPLIT){
				
				//get the transition rule:
				$transitonRule = $prevConnector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
				if(!is_null($transitonRule) && $transitonRule instanceof core_kernel_classes_Resource){
					
					$then = $transitonRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN));
					if(!is_null($then) && $then instanceof core_kernel_classes_Resource){
						if($then->uriResource == $activity->uriResource){
						
						}
					}
				
				}
			
			}
			*/
		}
		
		//clean reference in transition rule (faster method)
		$thenCollection = $apiModel->getSubject(PROPERTY_TRANSITIONRULES_THEN , $activity->uriResource);
		foreach($thenCollection->getIterator() as $transitionRule){
			$apiModel->removeStatement($transitionRule->uriResource, PROPERTY_TRANSITIONRULES_THEN, $activity->uriResource, '');
		}
		$elseCollection = $apiModel->getSubject(PROPERTY_TRANSITIONRULES_ELSE , $activity->uriResource);
		foreach($elseCollection->getIterator() as $transitionRule){
			$apiModel->removeStatement($transitionRule->uriResource, PROPERTY_TRANSITIONRULES_ELSE, $activity->uriResource, '');
		}
			
		//delete activity itself:
		$returnValue = $this->deleteInstance($activity);
		
		return $returnValue;
	}
	
	/**
     * delete a connector and its related resources
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource connector
     * @return boolean
     */
	public function deleteConnector(core_kernel_classes_Resource $connector){
		
		$returnValue = false;
		
		if(!self::isConnector($connector)){
			throw new Exception("the resource in the parameter is not a connector");
			return $returnValue;
		}
		
		//get the type of connector:
		$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
		if(!is_null($connectorType) && $connectorType instanceof core_kernel_classes_Resource){
			if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_SPLIT){
				//delete the related rule:
				$relatedRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
				if(!is_null($relatedRule)){
					$this->deleteRule($relatedRule);
				}
			}
		}
		
		
		//manage the connection to the previous activities: clean the reference to this connector:
		$previousActivityCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
		foreach($previousActivityCollection->getIterator() as $previousActivity){
			if($this->isConnector($previousActivity)){
				core_kernel_classes_ApiModelOO::singleton()->removeStatement($previousActivity->uriResource, PROPERTY_CONNECTORS_NEXTACTIVITIES, $connector->uriResource, '');
			}
		}
		
		//manage the connection to the following activities
		$activityRef = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
		$nextActivityCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
		foreach($nextActivityCollection->getIterator() as $nextActivity){
			if($this->isConnector($nextActivity)){
				$nextActivityRef = $nextActivity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
				if($nextActivityRef == $activityRef){
					$this->deleteConnector($nextActivity);//delete following connectors only if they have the same activity reference
				}
			}
		}
		
		//delete connector itself:
		$returnValue = $this->deleteInstance($connector);
		
		return $returnValue;
	}
	
	/**
     * delete the reference to an object via a given property
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource property
	 * @param  core_kernel_classes_Resource object
	 * @param  boolean multiple
     * @return boolean
     */
	public function deleteReference(core_kernel_classes_Property $property, core_kernel_classes_Resource $object, $multiple = false){
		
		$returnValue = false;
		
		$apiModel = core_kernel_classes_ApiModelOO::singleton();
		
		$subjectCollection = $apiModel->getSubject($property->uriResource, $object->uriResource);
		if(!$subjectCollection->isEmpty()){
			if($multiple){
				$returnValue = true;
				foreach($subjectCollection->getIterator() as $subject){
					if( !$apiModel->removeStatement($subjectCollection->get(0)->uriResource, $property->uriResource, $object->uriResource, '') ){
						$returnValue = false;
						break;
					}
				}
			}else{
				$returnValue = $apiModel->removeStatement($subjectCollection->get(0)->uriResource, $property->uriResource, $object->uriResource, '');
			}
		}else{
			$returnValue = true;
		}
		
		return $returnValue;
	}
	
    /**
     * Check whether the class is authorized 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function isAuthorizedClass( core_kernel_classes_Class $clazz){
	
        $returnValue = (bool) false;

		$authorizedClassUri=array(
			CLASS_ACTIVITIES,
			CLASS_PROCESSVARIABLES,
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
		
        return (bool) $returnValue;
    }
	
	/**
     * Create an activity for a process
     *
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource process
	 * @param  string label
     * @return core_kernel_classes_Resource
     */	
	public function createActivity(core_kernel_classes_Resource $process, $label=''){
		
		$activity = null;
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
	
	/**
     * Create a connector for an activity
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
	 * @param  string label
     * @return core_kernel_classes_Resource
     */	
	public function createConnector(core_kernel_classes_Resource $activity, $label=''){
		$connectorLabel = "";
		if(empty($label)){
			$connectorLabel = $activity->getLabel()."_c";//warning: could exist duplicate for children of a split connector
		}else{
			$connectorLabel = $label;
		}
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$connector = $connectorClass->createInstance($connectorLabel, "created by ProcessAuthoringService.Class");
		
		if(!empty($connector)){
			//associate the connector to the activity
			$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES), $activity->uriResource);
			
			//set the activity reference of the connector:
			$activityRefProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE);
			if($this->isActivity($activity)){
				$connector->setPropertyValue($activityRefProp, $activity->uriResource);
			}elseif($this->isConnector($activity)){
				$connector->setPropertyValue($activityRefProp, $activity->getUniquePropertyValue($activityRefProp)->uriResource);
			}else{
				throw new Exception("invalid resource type for the activity parameter: {$activity->uriResource}");
			}
		}else{
			throw new Exception("the connector cannot be created for the activity {$activity->uriResource}");
		}
		return $connector;
	}
	
	/**
     * Description
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource connector
	 * @param  core_kernel_classes_Resource followingActivity
	 * @param  string newActivityLabel
     * @return void
     */	
	public function createSequenceActivity(core_kernel_classes_Resource $connector, core_kernel_classes_Resource $followingActivity = null, $newActivityLabel = ''){
		if(is_null($followingActivity)){
			//get the process associate to the connector to create a new instance of activity
			$relatedActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));
			$processCollection = core_kernel_classes_ApiModelOO::getSubject(PROPERTY_PROCESS_ACTIVITIES, $relatedActivity->uriResource);
			if(!$processCollection->isEmpty()){
				$followingActivity = $this->createActivity($processCollection->get(0), $newActivityLabel);
				$newConnector = $this->createConnector($followingActivity);
			}else{
				throw new Exception("no related process instance found to create an activity");
			}
		}
		if($followingActivity instanceof core_kernel_classes_Resource){
			//associate it to the property value of the connector
			$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $followingActivity->uriResource);//use this function and not editPropertyValue!
		}
	}
	
	/**
	 * Create a rule according to a condition
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource connector
	 * @param  string condiiton
     * @return boolean
     */	
	public function createRule(core_kernel_classes_Resource $connector, $condition=''){
		
		$returnValue = true;
		
		//place the following bloc in a helper
		if (!empty($condition))
			$question = $condition;
		else
			$question = "";
		
		//question test:
		//$question = "IF    (11+B_Q01a*3)>=2 AND (B_Q01c=2 OR B_Q01c=7)    	THEN ^variable := 2*(B_Q01a+7)-^variable";
		
		//analyse the condiiton string and convert to an XML document:
		if (get_magic_quotes_gpc()) $question = stripslashes($question);// Magic quotes are deprecated

		if (!empty($question)){ // something to parse
			// str_replace taken from the MsReader class
			$question = str_replace("’", "'", $question); // utf8...
			$question = str_replace("‘", "'", $question); // utf8...
			$question = str_replace("“", "\"", $question);
			$question = str_replace("”", "\"", $question);
			$question = "if ".$question;
			try{
				$analyser = new Analyser();
				$tokens = $analyser->analyse($question);

				// $xml = htmlspecialchars($tokens->getXmlString(true));
				// $xml = $tokens->getXmlString(true);
				$xmlDom = $tokens->getXml();
				
			}catch(Exception $e){
				throw new Exception("CapiXML error: {$e->getMessage()}");
			}
		}
		
		//create the expression instance:
		$expressionInstance = null;
		foreach ($xmlDom->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "condition"){
					
					$conditionDescriptor = DescriptorFactory::getConditionDescriptor($childOfChildNode);
					$expressionInstance = $conditionDescriptor->import();//(3*(^var +  1) = 2 or ^var > 7) AND ^RRR
					break 2;//once is enough...
				
				}
			}
		}
		
		if($expressionInstance instanceof core_kernel_classes_Resource){
			//associate the newly create expression with the transition rule of the connector
			$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
			if(empty($transitionRule)){
				//create an instance of transition rule:
				$transitionRuleClass = new core_kernel_classes_Class(CLASS_TRANSITIONRULES);
				$transitionRule = $transitionRuleClass->createInstance();
				//Associate the newly created transition rule to the connector:
				$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), $transitionRule->uriResource);
			}
			$returnValue = $transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_RULE_IF), $expressionInstance->uriResource);
		}
		
		return $returnValue;
	}
		
	/**
     * Description
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource connector
	 * @param  string connectorType
	 * @param  core_kernel_classes_Resource followingActivity
	 * @param  string newActivityLabel
	 * @param  boolean followingActivityisConnector
     * @return void
     */	
	public function createSplitActivity(core_kernel_classes_Resource $connector, $connectorType, core_kernel_classes_Resource $followingActivity = null, $newActivityLabel ='', $followingActivityisConnector = false){
		//remove property PROPERTY_CONNECTORS_NEXTACTIVITIES values on connector before:
		if(is_null($followingActivity)){
			
			if($followingActivityisConnector){
				//create a new connector:
				$followingActivity = $this->createConnector($connector);
			}else{
				//get the process associate to the connector to create a new instance of activity
				$relatedActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));
				$processCollection = core_kernel_classes_ApiModelOO::getSubject(PROPERTY_PROCESS_ACTIVITIES, $relatedActivity->uriResource);
				if(!$processCollection->isEmpty()){
					$followingActivity = $this->createActivity($processCollection->get(0), $newActivityLabel);
					$newConnector = $this->createConnector($followingActivity);
				}else{
					throw new Exception("no related process instance found to create an activity");
				}
			}
		}
		
		if($followingActivity instanceof core_kernel_classes_Resource){
			//associate it to the property value of the connector
			$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $followingActivity->uriResource);//use this function and not editPropertyValue!
			$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
			
			if(empty($transitionRule)){
				//create an instance of transition rule:
				$transitionRuleClass = new core_kernel_classes_Class(CLASS_TRANSITIONRULES);
				$transitionRule = $transitionRuleClass->createInstance("ruleFor".$connector->getLabel(),"generated by ProcessAuthoringService");
				//associate it to the connector:
				$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), $transitionRule->uriResource);
			}
			
			if(strtolower($connectorType) == 'then'){
				$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), $followingActivity->uriResource);
			}elseif(strtolower($connectorType) == 'else'){
				$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE), $followingActivity->uriResource);
			}
		}
	}
	
	/**
     * Get an array of activities of the process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource process
     * @return array
     */	
	public function getActivitiesByProcess(core_kernel_classes_Resource $process){
		
		$returnValue = array();
		
		//eventually, put $processUri in a class property
		if(empty($process) && !empty($this->processUri)){
			$process = new core_kernel_classes_Resource($this->processUri);
		}
		if(is_null($process)){
			throw new Exception("the process cannot be null");
			return $returnValue;
		}
		
		
		foreach ($process->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES))->getIterator() as $activity){
			if($activity instanceof core_kernel_classes_Resource){
				$returnValue[$activity->uriResource] = $activity;
			}
		}
		
		return $returnValue;
	}
	
	/**
     * Get all connectors of a process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource process
     * @return array
     */	
	public function getConnectorsByProcess(core_kernel_classes_Resource $process){
		$activities = $this->getActivitiesByProcess($process);
		$connectors = array();
		foreach($activities as $activity){
			$tempConnectorArray = array();
			$tempConnectorArray = $this->getConnectorsByActivity($activity, array('next'));//connectors of connector are not included here!
			//use the property value: activity reference here:	
			
		}
	
	}
	
	/**
     * Get all connectors of an activity
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
	 * @param  array option
	 * @param  boolean isConnector
     * @return array
     */
	public function getConnectorsByActivity(core_kernel_classes_Resource $activity, $option=array(), $isConnector=false ){
			
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
		
			$previousConnectorsCollection=core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES, $activity->uriResource);
		
			foreach ($previousConnectorsCollection->getIterator() as $connector){
				if(!is_null($connector)){
					if($connector instanceof core_kernel_classes_Resource ){
						$returnValue['prev'][$connector->uriResource] = $connector; 
					}
				}
			}
		}
		
		if(in_array('next',$option)){
		
			$followingConnectorsCollection=core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activity->uriResource);
		
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
	
	/**
     * Check if the resource is an activity instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource resource
     * @return boolean
     */	
	public static function isActivity(core_kernel_classes_Resource $resource){
		$returnValue = false;
		
		$activityType = core_kernel_classes_ApiModelOO::singleton()->getObject($resource->uriResource, RDF_TYPE);
		if($activityType->count()>0){
			if($activityType->get(0) instanceof core_kernel_classes_Resource){//should be a generis class
				if( $activityType->get(0)->uriResource == CLASS_ACTIVITIES){
					$returnValue = true;
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
     * Check if the resource is a connector instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource resource
     * @return boolean
     */	
	public static function isConnector(core_kernel_classes_Resource $resource){
		$returnValue = false;
		
		$activityType = core_kernel_classes_ApiModelOO::singleton()->getObject($resource->uriResource, RDF_TYPE);
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
     * Get the process variable with a given code
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string code
     * @return core_kernel_classes_Resource
     */
	public function getProcessVariable($code){
		$returnValue = null;
		
		$processVarCollection = core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_CODE, $code);
		if(!$processVarCollection->isEmpty()){
			$returnValue = $processVarCollection->get(0);
		}
		
		return $returnValue;
	}
	
		

} /* end of class taoDelivery_models_classes_ProcessAuthoringService */

?>