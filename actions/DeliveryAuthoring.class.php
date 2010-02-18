<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');



/**
 * DeliveryAuthoring Controller provide actions to edit a delivery
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class DeliveryAuthoring extends TaoModule {
	
	protected $processTreeService = null;
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new taoDelivery_models_classes_ProcessAuthoringService();
		$this->defaultData();
		
		//add the tree service
		$this->processTreeService = new taoDelivery_models_classes_ProcessTreeService();
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected instance from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $instance
	 */
	protected function getCurrentInstance(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$instance = $this->service->getInstance($uri, 'uri', $clazz);
		if(is_null($instance)){
			throw new Exception("No instance of the class {$clazz->getLabel()} found for the uri {$uri}");
		}
		
		return $instance;
	}
	
	/**
	 * @see TaoModule::getRootClass
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return null;
	}
	
	

	protected function getCurrentActivity(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('activityUri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid activity uri found");
		}
		
		$instance = $this->service->getInstance($uri, 'uri', new core_kernel_classes_Class(CLASS_ACTIVITIES));
		if(is_null($instance)){
			throw new Exception("No instance of the class Activities found for the uri {$uri}");
		}
		
		return $instance;
	}
	
	protected function getCurrentProcess(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('processUri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid process uri found");
		}
		
		$instance = $this->service->getInstance($uri, 'uri', new core_kernel_classes_Class(CLASS_PROCESS));
		if(is_null($instance)){
			throw new Exception("No instance of the class Process found for the uri {$uri}");
		}
		
		return $instance;
	}

/*
 * controller actions
 */
	/**
	 * Render json data to populate the delivery tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getInstancesOf(){
				
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$instanceOf = strtolower($_GET["instanceof"]);
		$classUri='';
		switch($instanceOf){
			case 'servicedefinition': 
				$classUri=CLASS_SERVICESDEFINITION;// <=> CLASS_WEBSERVICES or CLASS_SUPPORTSERVICES
				break;
			case 'formalparameter': 
				$classUri=CLASS_FORMALPARAMETER;break;
			case 'activity': 
				$classUri=CLASS_ACTIVITIES;break;
			case 'role': 
				$classUri=CLASS_ROLE;break;
			default:
				throw new Exception('unknown class');break;
		}
		// $classUri = CLASS_SERVICEDEFINITION;
		//!!! currently, not the uri of the class is provided: better to pass it to "get" parameter somehow
		//one possibility: replace all by their uriResource from the authoring template.
		$clazz=new core_kernel_classes_Class($classUri);
		if( !$this->service->isAuthorizedClass($clazz) ){
			throw new Exception("wrong class uri in parameter");
		}
		
		$highlightUri = '';
		// if($this->hasSessionAttribute("showNodeUri")){
			// $highlightUri = $this->getSessionAttribute("showNodeUri");
			// unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		// }
		
		$filter = '';
		if($this->hasRequestParameter('filter')){
			$filter = $this->getRequestParameter('filter');
		}
		echo json_encode( $this->service->toTree( $clazz, true, true, $highlightUri, $filter));
	}
	
	public function getActivities(){
		//getCurrentProcess from delivery
		
		// $processUri = tao_helpers_Uri::decode($_POST["processUri"]);
		$processUri = "http://127.0.0.1/middleware/demo.rdf#i1265636054002217400";
		// $processUri = "http://www.tao.lu/middleware/taoqual.rdf#118589073043506";//item exemple itemtranslation
		if(!empty($processUri)){
			// echo json_encode($this->service->activityTree(new core_kernel_classes_Resource($processUri)));
			
			echo json_encode($this->processTreeService->activityTree(new core_kernel_classes_Resource($processUri)));
		}else{
			throw new Exception("no process uri found");
		}
	}
	
	public function getActivityTree(){
		$this->setView('process_tree_activity.tpl');
	}
	
	public function addActivity(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$label='';
		if(isset($_POST['label'])){
			$label = $_POST['label'];
		}
		
		$currentProcess = $this->getCurrentProcess();
		$newActivity = $this->service->createActivity($currentProcess, $label);
		$newConnector = $this->service->createConnector($newActivity);
		
		//attach the created activity to the process
		if(!is_null($newActivity) && $newActivity instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $newActivity->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($newActivity->uriResource),
				'connector' => $this->processTreeService->defaultConnectorNode($newConnector)
			));
		}
	}
	
	public function addInteractiveService(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$currentActivity = $this->getCurrentActivity();
		$newService = $this->service->createInteractiveService($currentActivity);
		if(!is_null($newService) && $newService instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $newService->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($newService->uriResource)
			));
		}
	}

	
	public function getSectionTrees(){
		$section = $_POST["section"];
		$this->setData('section', $section);
		$this->setView('process_tree.tpl');
	}
	
	public function editInstance(){
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		$formName="";
		//define the type of instance to be edited:
		if(strcasecmp($clazz->uriResource, CLASS_ACTIVITIES) == 0){
			$formName = "activity";
		}elseif(strcasecmp($clazz->uriResource, CLASS_FORMALPARAMETER) == 0){
			$formName = "formalParameter";
		}elseif(strcasecmp($clazz->uriResource, CLASS_ROLE) == 0){
			$formName = "role";
		}elseif( (strcasecmp($clazz->uriResource, CLASS_WEBSERVICES) == 0) || (strcasecmp($clazz->uriResource, CLASS_SUPPORTSERVICES) == 0) ){
			//note: direct instanciating CLASS_SERVICEDEFINITION should be forbidden
			$formName = "serviceDefinition";
		}elseif(strcasecmp($clazz->uriResource, CLASS_FORMALPARAMETER) == 0){
			$formName = "formalParameter";
		}else{
			throw new Exception("attempt to editing an instance of an unsupported class");
		}
				
		$myForm = null;
		$myForm = taoDelivery_helpers_ProcessFormFactory::instanceEditor($clazz, $instance, $formName, array("noSubmit"=>true,"noRevert"=>true) , array('http://www.tao.lu/middleware/Interview.rdf#122354397139712') );
		// $myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $instance, $formName);
		$myForm->setActions(array(), 'bottom');	
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$instance = $this->service->bindProperties($instance, $myForm->getValues());
				echo __("saved");exit;
			}
		}
		
		$this->setData('section', $formName);
		$this->setData('formPlus', $myForm->render());
		$this->setView('process_form_tree.tpl');
	}
	
	public function editActivityProperty(){
		$formName = "activityPropertyEditor";
		$activity = $this->getCurrentActivity();
		$excludedProperty = array(
			PROPERTY_ACTIVITIES_INTERACTIVESERVICES,
			PROPERTY_ACTIVITIES_ONAFTERINFERENCERULE,
			PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE,
			PROPERTY_ACTIVITIES_CONSISTENCYRULE
		);
		
		$this->setData('saved', false);
		
		$myForm = null;
		$myForm = taoDelivery_helpers_ProcessFormFactory::instanceEditor(new core_kernel_classes_Class(CLASS_ACTIVITIES), $activity, $formName, array("noSubmit"=>true,"noRevert"=>true), $excludedProperty);
		$myForm->setActions(array(), 'bottom');	
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$activity = $this->service->bindProperties($activity, $myForm->getValues());
				
				//replace with a clean template upload
				$this->setData('saved', true);
				$this->setView('process_form_activity.tpl');
				exit;
			}
		}
		
		$this->setData('section', $formName);
		$this->setData('myForm', $myForm->render());
		$this->setView('process_form_activity.tpl');
	}
	
	/**
	 * Add an instance        
	 * @return void
	 */
	public function addInstance(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$instance = $this->service->createInstance($clazz);
		if(!is_null($instance) && $instance instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $instance->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($instance->uriResource)
			));
		}
	}
		
	/**
	 * Delete a delivery or a delivery class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteInstance($this->getCurrentInstance());
		}
		// else{
			// $deleted = $this->service->deleteDeliveryClass($this->getCurrentClass());
		// }
		
		echo json_encode(array('deleted' => $deleted));
	}
	
	public function deleteCallOfService(){
		$callOfServiceUri = $_POST["uri"];
		
		//delete its related properties
		$deleted = $this->service->deleteActualParameters(new core_kernel_classes_Resource ($callOfServiceUri));
		
		//delete call of service itself
		$deleted = $this->service->deleteInstance(new core_kernel_classes_Resource ($callOfServiceUri));
	
		return $deleted;
	}
	
	
	/**
	 * Duplicate an instance
	 * A bit more complicated here
	 * @return void
	 */
	// public function cloneInstance(){
		// if(!tao_helpers_Request::isAjax()){
			// throw new Exception("wrong request mode");
		// }
		
		// $instance = $this->getCurrentInstance();
		// $clazz = $this->getCurrentClass();
		// if(! $this->service->isAuthorizedClass($clazz)){
			// throw new Exception("attempt to clone an instance of an unauthorized class!");
		// }
		// $clone = $this->service->createInstance($clazz);
		// if(!is_null($clone)){
			
			// foreach($clazz->getProperties() as $property){
				// foreach($instance->getPropertyValues($property) as $propertyValue){
					// $clone->setPropertyValue($property, $propertyValue);
				// }
			// }
			// $clone->setLabel($instance->getLabel()."'");
			// echo json_encode(array(
				// 'label'	=> $clone->getLabel(),
				// 'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			// ));
		// }
	// }
	
	public function editCallOfService(){
		$callOfServiceUri = tao_helpers_Uri::decode($_POST['uri']);
		
		$formName="callOfServiceEditor";
		$myForm = taoDelivery_helpers_ProcessFormFactory::callOfServiceEditor(new core_kernel_classes_Resource($callOfServiceUri), null, $formName);//NS_TAOQUAL . '#118595593412394'
		
		$this->setData('formId', $formName);
		$this->setData('formInteractionService', $myForm->render());
		$this->setView('process_form_interactiveServices.tpl');
	}
			
	public function saveCallOfService(){
		$saved = true;
		
		//decode uri:
		$data = array();
		foreach($_POST as $key=>$value){
			$data[tao_helpers_Uri::decode($key)] = tao_helpers_Uri::decode($value);
		}
		
		$callOfService = new core_kernel_classes_Resource($data["callOfServiceUri"]);
		unset($data["callOfServiceUri"]);
		
		//edit service definition resource value:
		if(!isset($data[PROPERTY_CALLOFSERVICES_SERVICEDEFINITION])){
			throw new Exception("no service definition uri found in POST");
		}
		$serviceDefinition = new core_kernel_classes_Resource($data[PROPERTY_CALLOFSERVICES_SERVICEDEFINITION]);
		unset($data[PROPERTY_CALLOFSERVICES_SERVICEDEFINITION]);
		
		//edit label
		$label = $data["label"];
		$this->service->bindProperties($callOfService, array(
			PROPERTY_CALLOFSERVICES_SERVICEDEFINITION => $serviceDefinition->uriResource,
			'http://www.w3.org/2000/01/rdf-schema#label' => $label
		));
		//note: equivalent to $callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
		
		//reset new actual parameters : clear ALL and recreate new values at each save
		$deleted = $this->service->deleteActualParameters($callOfService);
		if(!$deleted){
			throw new Exception("the actual parameters related to the call of service cannot be removed");
		}
		
		foreach($data as $key=>$value){
			$formalParamUri = '';
			$parameterInOrOut='';
			
			//find whether it is a parameter IN or OUT:
			
			//method 1: use the connection relation between the subject serviceDefinition and the object formalParameter: 
			//issue with the use of the same instance of formal parameter for both parameter in and out of an instance of a service definiton
			/*
			$formalParameterType = core_kernel_classes_ApiModelOO::getPredicate($serviceDefinition->uriResource, $formalParam->uriResource);
			if(strcasecmp($formalParameterType->uriResource, PROPERTY_SERVICESDEFINITION_FORMALPARAMIN)==0){
				$parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMIN;
			}elseif(strcasecmp($formalParameterType->uriResource, PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT)==0){
				$parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT;
			}else{
				//unknown actual parameter type to be bind to the current call of service
				continue;
			}
			*/
			
			//method2: use the suffix of the name of the form input:
			$index=0;
			if($index=strpos($key, '_IN')){
				$formalParamUri = substr($key,0,$index);
				$parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMIN;
			}elseif($index=strpos($key, '_OUT')){
				$formalParamUri = substr($key,0,$index);
				$parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT;
			}else{
				continue;
			}
			
			$formalParam = new core_kernel_classes_Resource($formalParamUri);
			$saved = $this->service->setActualParameter($callOfService, $formalParam, $value, $parameterInOrOut, '');
			if(!$saved){
				break;
			}
		}
		
		echo json_encode(array("saved" => $saved));
	}
	
	public function editConnector(){
		$connectorUri = tao_helpers_Uri::decode($_POST['connectorUri']);
		
		$formName="connectorEditor";
		$myForm = taoDelivery_helpers_ProcessFormFactory::connectorEditor(new core_kernel_classes_Resource($connectorUri), null, $formName);
		
		$this->setData('formId', $formName);
		$this->setData('formConnector', $myForm->render());
		$this->setView('process_form_connector.tpl');
	}
	
	public function saveConnector(){
		$saved = true;
		
		//decode uri:
		$data = array();
		foreach($_POST as $key=>$value){
			$data[tao_helpers_Uri::decode($key)] = tao_helpers_Uri::decode($value);
		}
		
		if(!isset($data["connectorUri"])){
			$saved = false;
			throw new Exception("no connector uri found in POST");
		}else{	
			$connectorInstance = new core_kernel_classes_Resource($data["connectorUri"]);
		}
		
		//edit service definition resource value:
		if(!isset($data[PROPERTY_CONNECTORS_TYPE])){
			$saved = false;
			throw new Exception("no connector type uri found in POST");
		}
		$this->service->bindProperties($connectorInstance, array(PROPERTY_CONNECTORS_TYPE => $data[PROPERTY_CONNECTORS_TYPE]));
		
		$followingActivity = null;
		if($data[PROPERTY_CONNECTORS_TYPE] == TYPEOFCONNECTORS_SEQUENCE){
			//get form input starting with "next_"
			if(isset($data["next_activityUri"])){
				if($data["next_activityUri"]=="newActivity"){
					$this->service->createSequenceActivity($connectorInstance, null, $data["next_activityLabel"]);
				}else{
					$followingActivity = new core_kernel_classes_Resource($data["next_activityUri"]);
					$this->service->createSequenceActivity($connectorInstance, $followingActivity);
				}
			}
		}elseif($data[PROPERTY_CONNECTORS_TYPE] == TYPEOFCONNECTORS_SPLIT){
			//delete the old rule:
			// $deleted = $this->service->deleteRule($transitionRule);
			// if(!$deleted){
				// throw new Exception("the transition rule related to the connector cannot be removed");
			// }
			//save the new rule here:
			
			
			//clean old value of property (use bind property with empty input?)
			$connectorInstance->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
			
			//save the activity in "THEN":
			if(isset($data['if'])){
				if(($data['then_activityOrConnector']=="activity") && isset($data["then_activityUri"])){
					if($data["then_activityUri"]=="newActivity"){
						$this->service->createSplitActivity($connectorInstance, 'then', null, $data["then_activityLabel"], false);
					}else{
						$followingActivity = new core_kernel_classes_Resource($data["then_activityUri"]);
						$this->service->createSplitActivity($connectorInstance, 'then', $followingActivity, '', false);
					}
				}elseif(($data['then_activityOrConnector']=="connector") && isset($data["then_connectorUri"])){
					if($data["then_connectorUri"]=="newConnector"){
						$this->service->createSplitActivity($connectorInstance, 'then', null, '', true);
					}else{
						$followingActivity = new core_kernel_classes_Resource($data["then_connectorUri"]);
						$this->service->createSplitActivity($connectorInstance, 'then', $followingActivity, '', true);
					}
				}
			
				//save the activity in "ELSE":
				if(($data['else_activityOrConnector']=="activity") && isset($data["else_activityUri"])){
					if($data["else_activityUri"]=="newActivity"){
						$this->service->createSplitActivity($connectorInstance, 'else', null, $data["else_activityLabel"], false);
					}else{
						$followingActivity = new core_kernel_classes_Resource($data["else_activityUri"]);
						$this->service->createSplitActivity($connectorInstance, 'else', $followingActivity, '', false);
					}
				}elseif(($data['else_activityOrConnector']=="connector") && isset($data["else_connectorUri"])){
					if($data["else_connectorUri"]=="newConnector"){
						$this->service->createSplitActivity($connectorInstance, 'else', null, '', true);
					}else{
						$followingActivity = new core_kernel_classes_Resource($data["else_connectorUri"]);
						$this->service->createSplitActivity($connectorInstance, 'else', $followingActivity, '', true);
					}
				}
			}
		}
		
		echo json_encode(array("saved" => $saved));
	}
	
	public function saveRule(){
		$condition = '';
		$this->service->createRule($condition);
	}
	/**
	 *
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function preview(){
		
	}
			
	/*
	 * @TODO implement the following actions
	 */
	
	public function getMetaData(){
		throw new Exception("Not implemented yet");
	}
	
	public function saveComment(){
		throw new Exception("Not implemented yet");
	}
	
}
?>