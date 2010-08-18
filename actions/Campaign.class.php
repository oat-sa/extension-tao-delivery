<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');
require_once(BASE_PATH.'/models/classes/class.CampaignService.php');

/**
 * Campaign Controller provide actions performed from url resolution
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class Campaign extends TaoModule {
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new taoDelivery_models_classes_CampaignService();
		$this->defaultData();
		
		Session::setAttribute('currentSection', 'campaign');
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected campaign from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $campaign
	 */
	private function getCurrentCampaign(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		
		$campaign = $this->service->getCampaign($uri, 'uri', $clazz);
		if(is_null($campaign)){
			throw new Exception("No campaign found for the uri {$uri}");
		}
		
		return $campaign;
	}
	
	/**
	 * @see TaoModule::getCurrentInstance
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentInstance(){
		return $this->getCurrentCampaign();
	}
	
	/**
	 * @see TaoModule::getRootClass
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getCampaignClass();
	}
	
/*
 * controller actions
 */
	/**
	 * Render json data to populate the campaign tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getCampaigns(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$options = array(
			'subclasses' => true, 
			'instances' => true, 
			'highlightUri' => '', 
			'labelFilter' => '', 
			'chunk' => false
		);
		if($this->hasRequestParameter('filter')){
			$options['labelFilter'] = $this->getRequestParameter('filter');
		}
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = $this->service->getCampaignClass();
		}
		
		echo json_encode( $this->service->toTree($clazz , $options));
	}
	
	/**
	 * Edit a campaign class
	 * @return void
	 */
	public function editCampaignClass(){
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getCampaignClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Campaign Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit campaign class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Edit a delviery instance
	 * @return void
	 */
	public function editCampaign(){
		$clazz = $this->getCurrentClass();
		
		$campaign = $this->getCurrentCampaign();
		
		$formContainer = new tao_actions_form_Instance($clazz, $campaign);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$campaign = $this->service->bindProperties($campaign, $myForm->getValues());
				
				$this->setData('message', __('Campaign saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($campaign->uriResource));
		
		//get the deliveries related to this delivery campaign
		$relatedTests = tao_helpers_Uri::encodeArray($this->service->getRelatedDeliveries($campaign), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('relatedDeliveries', json_encode($relatedTests));
		$this->setData('index', '1');
		
		$this->setData('formTitle', __('Edit Campaign'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_campaign.tpl');
	}
	
	/**
	 * Add a campaign instance        
	 * @return void
	 */
	public function addCampaign(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$campaign = $this->service->createInstance($clazz, $this->service->createUniqueLabel($clazz));
		if(!is_null($campaign) && $campaign instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $campaign->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($campaign->uriResource)
			));
		}
	}
	
	/**
	 * Add a campaign subclass
	 * @return void
	 */
	public function addCampaignClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createCampaignClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Delete a campaign or a campaign class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteCampaign($this->getCurrentCampaign());
		}
		else{
			$deleted = $this->service->deleteCampaignClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Duplicate a campaign instance
	 * @return void
	 */
	public function cloneCampaign(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$campaign = $this->getCurrentCampaign();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($campaign->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($campaign->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * Get the data to populate the tree of deliveries
	 * @return void
	 */
	public function getDeliveries(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * Save the delivery related deliveries
	 * @return void
	 */
	public function saveDeliveries(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$deliveries = array();
			
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($deliveries, tao_helpers_Uri::decode($value));
			}
		}
		
		if($this->service->setRelatedDeliveries($this->getCurrentCampaign(), $deliveries)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Main action
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		$this->setView('index_campaign.tpl');
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