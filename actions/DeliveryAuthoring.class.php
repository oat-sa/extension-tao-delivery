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
				$classUri=CLASS_SERVICEDEFINITION;// <=> CLASS_WEBSERVICES or CLASS_SUPPORTSERVICES
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
	
	/**
	 * Edit a class ... allowed??
	 * @see tao_helpers_form_GenerisFormFactory::classEditor
	 * @return void
	 */
	// public function editDeliveryClass(){
		// $clazz = $this->getCurrentClass();
		// $myForm = $this->editClass($clazz, $this->service->getDeliveryClass());
		// if($myForm->isSubmited()){
			// if($myForm->isValid()){
				// if($clazz instanceof core_kernel_classes_Resource){
					// $this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				// }
				// $this->setData('message', 'delivery class saved');
				// $this->setData('reload', true);
				// $this->forward('Delivery', 'index');
			// }
		// }
		// $this->setData('formTitle', 'Edit delivery class');
		// $this->setData('myForm', $myForm->render());
		// $this->setView('form.tpl');
	// }
	
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
		echo $myForm; 
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
	 * Add a delivery subclass
	 * @return void
	 */
	 //same: unquote and edit only if additional class is allowed in the workflow model
	// public function addDeliveryClass(){
		// if(!tao_helpers_Request::isAjax()){
			// throw new Exception("wrong request mode");
		// }
		// $clazz = $this->service->createDeliveryClass($this->getCurrentClass());
		// if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			// echo json_encode(array(
				// 'label'	=> $clazz->getLabel(),
				// 'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			// ));
		// }
	// }
	
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
		$callOfServiceUri = $_POST["uri"];
		//compare service definition resource value:
		
		//change it if it is different and delete all related actual parameters
		
		//set new actual parameters : clear ALL and recreate new values at each save
		$deleted = $this->service->deleteActualParameters(new core_kernel_classes_Resource($callOfServiceUri));
		
		if(empty($inputUri)){//place ce bloc dans la creation de call of service: cad retrouver systematiquement l'actual parameter associé à chaque fois, à partir du formal parameter et call of service, lors de la sauvegarde
			//if no actual parameter has been found above (since $inputUri==0) create an instance of actual parameter and associate it to the call of service:
			$property_actualParam_formalParam = new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_FORMALPARAM);
			$class_actualParam = new core_kernel_classes_Class(CLASS_ACTUALPARAM);
			$newActualParameter = $class_actualParam->createInstance($formalParam->getLabel(), "created by DeliveryAuthoring.Class");
			$newActualParameter->setPropertyValue($property_actualParam_formalParam, $formalParam->uriResource);
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