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
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new taoDelivery_models_classes_ProcessAuthoringService();
		$this->defaultData();
		
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected instance from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $instance
	 */
	private function getCurrentInstance(){
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
	
		
	public function getSectionTrees(){
		$section = $_POST["section"];
		$this->setData('section', $section);
		$this->setView('tree.tpl');
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
				echo "saved";exit;
			}
		}
		
		$this->setData('section', $formName);
		$this->setData('formPlus', $myForm->render());
		$this->setView('tree_form.tpl');
	}
	
	public function editCallOfService(){
		$myForm = taoDelivery_helpers_ProcessFormFactory::callOfServiceEditor(new core_kernel_classes_Resource(NS_TAOQUAL . '#118595593412394'));
		$this->setData('formInteractionService', $myForm->render());
		$this->setView('form_interactiveServices.tpl');
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
		
		echo json_encode(array('deleted'	=> $deleted));
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
	 * @return void
	 */
	public function cloneInstance(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$instance = $this->getCurrentInstance();
		$clazz = $this->getCurrentClass();
		if(! $this->service->isAuthorizedClass($clazz)){
			throw new Exception("attempt to clone an instance of an unauthorized class!");
		}
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($instance->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($instance->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	public function saveCallOfService(){
		
		//decode uri:
		$data = array();
		foreach($_POST as $key=>$value){
			$data[tao_helpers_Uri::decode($key)] = tao_helpers_Uri::decode($value);
		}
		
		$callOfService = new core_kernel_classes_Resource($data["callOfServiceUri"]);
		unset($data["callOfServiceUri"]);
		
		//edit service definition resource value:
		$serviceDefinition = new core_kernel_classes_Resource($data["serviceDefinitionUri"]);
		unset($data["serviceDefinitionUri"]);
		// $callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
		$this->service->bindProperties($callOfService, array(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION=>$serviceDefinition->uriResource));
		
		//reset new actual parameters : clear ALL and recreate new values at each save
		$deleted = $this->service->deleteActualParameters($callOfService);
		
		//Place the bloc below in the service after applying uridecode to every key (and value?)
		foreach($data as $key=>$value){
			$formalParamUri = $key;
			
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
			$this->service->setActualParameter($callOfService, $formalParam, $value, $parameterInOrOut, '')
			
		}
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