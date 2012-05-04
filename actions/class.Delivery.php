<?php
/**
 * Delivery Controller provide actions performed from url resolution
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class taoDelivery_actions_Delivery extends tao_actions_TaoModule {
	
	/**
	 * constructor: initialize the service and the default data
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return Delivery
	 */
	public function __construct(){
		
		if(isset($_GET['STANDALONE_MODE']) && (bool) $_GET['STANDALONE_MODE']){
			tao_helpers_Context::load('STANDALONE_MODE');
		}
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = taoDelivery_models_classes_DeliveryService::singleton();
		$this->defaultData();
		
		Session::setAttribute('currentSection', 'delivery');
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected delivery from the current context (from the uri and classUri parameter in the request)
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return core_kernel_classes_Resource $delivery
	 */
	private function getCurrentDelivery(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$delivery = $this->service->getDelivery($uri, 'uri', $clazz);
		if(is_null($delivery)){
			throw new Exception("No delivery found for the uri {$uri}");
		}
		
		return $delivery;
	}
	
	/**
	 * @see TaoModule::getCurrentInstance
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentInstance(){
		return $this->getCurrentDelivery();
	}
	
	/**
	 * @see TaoModule::getRootClass
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getDeliveryClass();
	}
	
/*
 * controller actions
 */
	/**
	 * Render json data to populate the delivery tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getDeliveries(){
		
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
			$clazz =  $this->service->getDeliveryClass();
		}
		
		echo json_encode( $this->service->toTree($clazz , $options));
	}
	
	/**
	 * Edit a delivery class
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function editDeliveryClass(){
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getDeliveryClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Delivery Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit delivery class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Edit a delviery instance
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function editDelivery(){
		$clazz = $this->getCurrentClass();
		$delivery = $this->getCurrentDelivery();
		
		$formContainer = new tao_actions_form_Instance($clazz, $delivery);
		$myForm = $formContainer->getForm();
		
		$maxExecElt	= $myForm->getElement(tao_helpers_Uri::encode(TAO_DELIVERY_MAXEXEC_PROP));
		if(!is_null($maxExecElt)){
			$maxExecElt->addValidators(array(
					tao_helpers_form_FormFactory::getValidator('Integer', array('min' => 1))
				));
			$myForm->addElement($maxExecElt);
		}
		
		$periodEndElt = $myForm->getElement(tao_helpers_Uri::encode(TAO_DELIVERY_END_PROP));
		if(!is_null($periodEndElt)){
			
			$periodEndElt->addValidators(array(
				tao_helpers_form_FormFactory::getValidator(
					'DateTime', 
					array(
						'comparator' => '>=',
						'datetime2_ref' => $myForm->getElement(tao_helpers_Uri::encode(TAO_DELIVERY_START_PROP))
					)
				)
			));
			$myForm->addElement($periodEndElt);
		}
		
		
		$resultServerElt = $myForm->getElement(tao_helpers_Uri::encode(TAO_DELIVERY_RESULTSERVER_PROP));
		if(!is_null($resultServerElt)){
			$resultServerElt->addValidators(array(
					tao_helpers_form_FormFactory::getValidator('NotEmpty')
				));
			$myForm->addElement($resultServerElt);
		}
		
		$myForm->evaluate();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$propertyValues = $myForm->getValues();
				
				//check if the authoring mode has changed: if advanced->simple, modify the related process to make it compatible
				if(array_key_exists(TAO_DELIVERY_AUTHORINGMODE_PROP, $propertyValues)){
					if($propertyValues[TAO_DELIVERY_AUTHORINGMODE_PROP] == TAO_DELIVERY_SIMPLEMODE){
						if($delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP))->uriResource == TAO_DELIVERY_ADVANCEDMODE){
							//get all tests from the process, then save them:
							$this->service->linearizeDeliveryProcess($delivery);
						}
					}
				}
				
				//then save the property values as usual
				$delivery = $this->service->bindProperties($delivery, $propertyValues);
				
				//edit process label:
				$this->service->updateProcessLabel($delivery);
				
				$this->setData('message', __('Delivery saved'));
				$this->setData('reload', true);
			}
		}
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($delivery->uriResource));
		
		//delivery authoring mode:
		$this->setData('authoringMode', 'simple');
		$authoringMode = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP));
		
		$myForm->removeElement(tao_helpers_Uri::encode(TAO_DELIVERY_AUTHORINGMODE_PROP));
		
		//remove the authoring buttons
		$myForm->removeElement(tao_helpers_Uri::encode(TAO_DELIVERY_DELIVERYCONTENT));
		$myForm->removeElement(tao_helpers_Uri::encode(TAO_DELIVERY_PROCESS));
		
		if($authoringMode->uriResource == TAO_DELIVERY_ADVANCEDMODE){
			$this->setData('authoringMode', 'advanced');
		}else{
			//the default option is the simple mode:
			$allTests = array();
			foreach($this->service->getAllTests() as $testUri => $testLabel){
				$allTests['test_'.tao_helpers_Uri::encode($testUri)] = $testLabel;
			}
			$this->setData('allTests', json_encode($allTests));
			
			$relatedTest = array();
			$testSequence = array();
			$i = 1;
			foreach($this->service->getDeliveryTests($delivery) as $test){
				$relatedTest[] = tao_helpers_Uri::encode($test->uriResource);
				if(!$test->isClass()){
					$testSequence[$i] = array(
						'uri' 	=> tao_helpers_Uri::encode($test->uriResource),
						'label' => $test->getLabel()
					);
					$i++;
				}
			}
			$this->setData('testSequence', $testSequence);
			
			$this->setData('relatedTests', json_encode($relatedTest));
		}
		
		//get the campaign(s) related to this delivery
		$relatedCampaigns = tao_helpers_Uri::encodeArray($this->service->getRelatedCampaigns($delivery), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('relatedCampaigns', json_encode($relatedCampaigns));
		
		//get the subjects related to the test(s) of the current delivery!	
		$excludedSubjects = tao_helpers_Uri::encodeArray($this->service->getExcludedSubjects($delivery), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('excludedSubjects', json_encode($excludedSubjects));
		
		//compilation state:
		$isCompiled = $this->service->isCompiled($delivery);
		$this->setData("isCompiled", $isCompiled);
		if($isCompiled){
			$this->setData("compiledDate", $this->service->getCompiledDate($delivery));
		}
		
		//get history stats:
		$histories = $this->service->getHistory($delivery);
		$this->setData("executionNumber", count($histories));
		
		$this->setData('uri', tao_helpers_Uri::encode($delivery->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('deliveryGroups', json_encode(array_map("tao_helpers_Uri::encode", $this->service->getDeliveryGroups($delivery))));
		$this->setData('formTitle', __('Delivery properties'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_delivery.tpl');
	}
	
	public function advancedMode(){
		$this->setAuthoringMode('advanced');
	}
	
	public function simpleMode(){
		$this->setAuthoringMode('simple');
	}
	
	private function setAuthoringMode($mode){
		$mode = strtolower($mode);
		if($mode != 'simple' && $mode != 'advanced'){
			throw new Exception('invalid mode');
		}
		
		$delivery = $this->getCurrentDelivery();
		$clazz = $this->getCurrentClass();
		
		$this->service->setAuthoringMode($delivery, $mode);
		
		$param = array(
			'uri' => tao_helpers_Uri::encode($delivery->uriResource),
			'classUri' => tao_helpers_Uri::encode($clazz->uriResource)
		);
		
		//reload the form, thus let the advanced authoring tab be available
		$this->redirect(tao_helpers_Uri::url('editDelivery', 'Delivery', null, $param));
	}
	
	/**
	 * Add a delivery instance 
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}	 
	 * @return void
	 */
	public function addDelivery(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$delivery = $this->service->createInstance($clazz, $this->service->createUniqueLabel($clazz));
		
		if(!is_null($delivery) && $delivery instanceof core_kernel_classes_Resource){
			
			echo json_encode(array(
				'label'	=> $delivery->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($delivery->uriResource)
			));
		}
	}
	
	/**
	 * Add a delivery subclass
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function addDeliveryClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createDeliveryClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Delete a delivery or a delivery class
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteDelivery($this->getCurrentDelivery());
		}
		else{
			$deleted = $this->service->deleteDeliveryClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Duplicate a delivery instance
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function cloneDelivery(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$clone = $this->service->cloneDelivery($this->getCurrentDelivery(), $this->getCurrentClass());
		if(!is_null($clone)){
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * display the authoring  template
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}	 
	 * @return void
	 */
	public function authoring(){
		$this->setData('error', false);
		try{
			
			//get process instance to be authored
			 $delivery = $this->getCurrentDelivery();
			 $processDefinition = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
			// $processDefinition = new core_kernel_classes_Resource("http://127.0.0.1/middleware/demo.rdf#i1265636054002217401");		
			$this->setData('processUri', tao_helpers_Uri::encode($processDefinition->uriResource));
		}
		catch(Exception $e){
			$this->setData('error', true);
			$this->setData('errorMessage', $e);
		}
		$this->setView('authoring/process_authoring_tool.tpl');
	}
	
	/**
	 * Get the data to populate the tree of delivery's subjects
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function getSubjects(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		if($this->hasRequestParameter('offset')){
			$options['offset'] = $this->getRequestParameter('offset');
		}
		if($this->hasRequestParameter('limit')){
			$options['limit'] = $this->getRequestParameter('limit');
		}
		if($this->hasRequestParameter('subclasses')){
			$options['subclasses'] = $this->getRequestParameter('subclasses');
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * Save the delivery excluded subjects
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function saveSubjects(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$subjects = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($subjects, tao_helpers_Uri::decode($value));
			}
		}
		
		if($this->service->setExcludedSubjects($this->getCurrentDelivery(), $subjects)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Get the data to populate the tree of delivery campaigns
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function getCampaigns(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
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
	 * Save the delivery related campaigns
	 * @access public
	 * @return void
	 */
	public function saveCampaigns(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$campaigns = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($campaigns, tao_helpers_Uri::decode($value));
			}
		}
		
		if($this->service->setRelatedCampaigns($this->getCurrentDelivery(), $campaigns)){
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
		$this->setData('section',Session::getAttribute('currentSection'));
		$this->setView('index.tpl');
	}
				
	/**
	 * Compile a test by providing its uri, via POST method.
	 * Its main purpose is to collect every required resource to run the test in a single folder so they become immediately available for the test launch, without any delay. 
	 * The resources are test and item runtime plugins and media files.
	 * This action parses the testContent and itemContent and save a copy of these files in the compiled test directory.
	 * The action compiles every available language for a given test at once.
	 * It provides a json string to indicate the success or failure of the test compilation
	 * The recompilation of an already compiled test will erase the previously created compiled files.
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function compile(){
		
        // execution time might be long...
        if (defined('TEST_COMPILATION_TIME')){
            set_time_limit(TEST_COMPILATION_TIME);
        }
        
		$resultArray = array();
		
		if(!$this->hasRequestParameter('deliveryUri')){
			throw new Exception('no delivery uri given in compile action');
		}
		if(!$this->hasRequestParameter('testUri')){
			throw new Exception('no test uri given in compile action');
		}
		
		$delivery = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('deliveryUri')));
		$test = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('testUri')));
		
		$resultArray = $this->service->compileTest($delivery, $test);
		if(isset($resultArray["failed"]['unexistingItems'])){
			foreach($resultArray["failed"]['unexistingItems'] as $itemUri => $item){
				$itemLabel = $item->getLabel();
				$resultArray["failed"]['unexistingItems'][$itemUri] = empty($itemLabel)?__('unknown label'):$itemLabel;
			}
		}
		
		echo json_encode($resultArray);
	}
	
	/**
	 * create history table
	 * @return void
	 */
	public function viewHistory(){
		
		$_SESSION['instances'] = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^uri_[0-9]+$/", $key)){
				$_SESSION['instances'][tao_helpers_Uri::decode($value)] = tao_helpers_Uri::decode($value);
			}
		}
		$this->setView("create_table.tpl");
	}
	
	/**
     * historyListing returns the execution history related to a given delivery (and subject)
     * 
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string deliveryUri
	 * @param  string subjectUri
     * @return array
     */
	public function getDeliveryHistory($delivery = null, $subject = null){
		
		$returnValue = array();
		$histories = array();
		
		//check deliveryUri validity
		if(empty($delivery)){
			$delivery = $this->getCurrentDelivery();
		}
		
		$histories = $this->service->getHistory($delivery, $subject);
		
		$propHistorySubject = new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP);
		$propHistoryTimestamp = new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP);
		$i=0;
		foreach ($histories as $history) {
		
			$returnValue[$i] = array();
			
			$subject = $history->getUniquePropertyValue($propHistorySubject);
			$returnValue[$i]["subject"] = $subject->getLabel(); //or $subject->literal to get the uri
			
			$timestamp = $history->getUniquePropertyValue($propHistoryTimestamp);
			$returnValue[$i]["time"] = date('d-m-Y G:i:s \(T\)', $timestamp->literal);
			
			$returnValue[$i]["uri"] = tao_helpers_Uri::encode($history->uriResource);
			$i++;
		}
			
		return $returnValue;
	}
	
	/**
	 * provide the user list data via json
	 * @return void
	 */
	public function historyData(){
	
		// $page = $this->getRequestParameter('page'); 
		// $limit = $this->getRequestParameter('rows');
		$page = 1;
		$limit = 500;
		// $sidx = $this->getRequestParameter('sidx');  
		// $sord = $this->getRequestParameter('sord'); 
		$start = $limit * $page - $limit; 
		
		// if(!$sidx) $sidx =1; 
		
		// $users = $this->userService->getAllUsers(array(
			// 'order' 	=> $sidx,
			// 'orderDir'	=> $sord,
			// 'start'		=> $start,
			// 'end'		=> $limit
		// ));
		$histories = $this->getDeliveryHistory($this->getCurrentDelivery());
		
		$count = count($histories); 
		if( $count >0 ) { 
			$total_pages = ceil($count/$limit); 
		}else { 
			$total_pages = 0; 
		} 
		if ($page > $total_pages){
			$page = $total_pages; 
		}
		
		$response = new stdClass();
		$response->page = $page; 
		$response->total = $total_pages; 
		$response->records = $count; 
		$i = 0;
		
		foreach($histories as $history) { 
			$cellData = array();
			$cellData[0] = $history['subject'];
			$cellData[1] = $history['time'];
			$cellData[2] = '';
			
			$response->rows[$i]['id']= tao_helpers_Uri::encode($history['uri']);
			$response->rows[$i]['cell'] = $cellData;
			$i++;
			
		}
		
		echo json_encode($response); 
	}
	
	public function deleteHistory(){
	
		$deleted = false;
		$message = __('An error occured during history deletion');
		if($this->hasRequestParameter('historyUri')){
			$history = new core_kernel_classes_Resource(tao_helpers_Uri::decode(tao_helpers_Uri::decode($this->getRequestParameter('historyUri'))));
			if($this->service->deleteHistory($history)){
				$deleted = true;
				$message = __('History deleted successfully');
			}
		}
		
		echo json_encode(array(
			'deleted' => $deleted,
			'message' => $message
		));
	}
	
	/*
	public function cache(){
		//get the id of subscribed modules and connect to them
		core_kernel_impl_ApiModelOO::getSubscription() : array(ids) NOT defined yet?
		core_kernel_impl_ApiModelOO::connectOnRemoteModule($idSubscription) : boolean NIY
		
		//get rdf file of these modules AND parse the rdf files (input: rdf dom object? or file location?)
		core_kernel_impl_ApiModelOO::exportXmlRdf() : String NIY

		//generate a new and unique namespace (thus new modelId) for the (each?) modules
		//store the triplets with this namespace:
		core_kernel_impl_ApiModelOO::importXmlRdf( java_lang_String $targetNameSpace,  java_lang_String $fileLocation) : Boolean NIY

		//save the reference to these new local namespaces ("s" for several modules) in the cache properties (one value for each cached module) of the 
		//q? given a delivery and a module, allow multiple cache? should be no, since delivery is aimed at being used within a limited time period
		
		//return the success/failure status of each distant module caching
	}
	*/
	
	/**
	 * services to render the delivery tests
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return 
	 */
	public function getTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		if($this->hasRequestParameter('uri')){
			$tests = $this->service->getRelatedTests(
				$this->getCurrentInstance()
			);
			$this->setData('tests', $tests);
			$this->setView('deliveryTests.tpl');
		}
	}
	
	/**
	 * get all the tests instances in a json response
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function getDeliveriesTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$tests = tao_helpers_Uri::encodeArray($this->service->getDeliveriesTests(), tao_helpers_Uri::ENCODE_ARRAY_KEYS);
		echo json_encode(array('data' => $tests));
	}
	
	/**
	 * get all the tests instances in a json response
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function getAllTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_TEST_CLASS);
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
	 * Save the delivery related tests
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function saveTests(){
		if(!tao_helpers_Request::isAjax()){
		//	throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$tests = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($tests, new core_kernel_classes_Resource(tao_helpers_Uri::decode($value)));
			}
		}
		
		if($this->service->setDeliveryTests($this->getCurrentDelivery(), $tests)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * get the compilation view
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function compileView(){
	
		$delivery = $this->getCurrentDelivery();
		
		$this->setData("processUri", tao_helpers_Uri::encode($delivery->uriResource));//currentprocess
		$this->setData("processLabel", $delivery->getLabel());
		$this->setData("deliveryClass", tao_helpers_Uri::encode($this->getCurrentClass()->uriResource));
		
		//compilation state:
		$isCompiled = $this->service->isCompiled($delivery);
		$this->setData("isCompiled", $isCompiled);
		if($isCompiled){
			$this->setData("compiledDate", $this->service->getCompiledDate($delivery));
		}
		
		$this->setView("delivery_compiling.tpl");
	}
	
	/**
	 * get the compilation view
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function initCompilation(){
		
		$delivery = $this->getCurrentDelivery();
		
		//init the value to be returned	
		$deliveryData=array();
		
		$deliveryData["uri"] = $delivery->uriResource;
		
		//check if a wsdl contract is set to upload the result:
		$resultServer = $this->service->getResultServer($delivery);
		$deliveryData['resultServer'] = $resultServer;
		
		$deliveryData['tests'] = array();
		if(!empty($resultServer)){
			
			//get the tests list from the delivery id: likely, by parsing the deliveryContent property value
			//array of resource, test set
			$tests = array();
			$tests = $this->service->getRelatedTests($delivery);
			
			foreach($tests as $test){
				$deliveryData['tests'][] = array(
					"label" => $test->getLabel(),
					"uri" => $test->uriResource
				);//url encode maybe?
			}
		}
		
		echo json_encode($deliveryData);
	}
	
	/**
	 * End the compilation of a delivery
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function endCompilation(){
	
		$delivery = $this->getCurrentDelivery();
		
		$response = array();
		$response["result"]=0;
		
		//generate the actual delivery process:		
		
		$generationResult = $this->service->generateProcess($delivery);
		
		if($generationResult['success']){
			if ($this->service->containsHumanAssistedMeasurements($delivery)) {
				$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CODINGMETHODE_PROP), TAO_DELIVERY_CODINGMETHODE_MANUAL);
				$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CODINGSTATUS_PROP), TAO_DELIVERY_CODINGSTATUS_GRADING);
			} else {
				$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CODINGMETHODE_PROP), TAO_DELIVERY_CODINGMETHODE_AUTOMATED);
			}
		}
		
		if($generationResult['success']){
			$propCompiled = new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP);
			if($delivery->editPropertyValues($propCompiled, GENERIS_TRUE)){
				$response = array(
					'result' => 1,
					'compiledDate' => $this->service->getCompiledDate($delivery)
				);
			}
		}else{
			$response['errors'] = array();
			if(isset($generationResult['errors']['delivery'])){
				$response['errors']['delivery'] = array();
				//bad design in delivery:
				$response['errors']['delivery']['initialActivity'] = $generationResult['errors']['delivery']['initialActivity'];
				$response['errors']['delivery']['isolatedConnectors'] = array();
				foreach($generationResult['errors']['delivery']['isolatedConnectors'] as $connector){
					$response['errors']['delivery']['isolatedConnectors'][] = $connector->getLabel();
				}
			}else{
				$i = 0;
				$response['errors']['tests'] = array();
				foreach($generationResult['errors']['tests'] as $testErrors){
					//bad design in some tests:
					$response['errors']['tests'][$i] = array(
						'initialActivity' => $testErrors['initialActivity'],
						'label' => $testErrors['resource']->getLabel(),
						'isolatedConnectors' => array()
					);
					
					// $response['errors']['tests'][$i]['initialActivity'] = $testErrors['initialActivity'];
					// $response['errors']['tests'][$i]['label'] = $testErrors['resource']->getLabel();
					// $response['errors']['tests'][$i]['isolatedConnectors'] = array();
					foreach($testErrors['isolatedConnectors'] as $connector){
						$response['errors']['tests'][$i]['isolatedConnectors'][] = $connector->getLabel();
					}
					
					$i++;
				}
			}
		}		
		
		echo json_encode($response);
	}
	
	/**
	 * get the list of groups to populate the checkbox tree of groups to link with
	 * @return void
	 */
	public function getGroups(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		if($this->hasRequestParameter('offset')){
			$options['offset'] = $this->getRequestParameter('offset');
		}
		if($this->hasRequestParameter('limit')){
			$options['limit'] = $this->getRequestParameter('limit');
		}
		if($this->hasRequestParameter('subclasses')){
			$options['subclasses'] = $this->getRequestParameter('subclasses');
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * save from the checkbox tree the groups to link with 
	 * @return void
	 */
	public function saveGroups(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		$groups = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($groups, tao_helpers_Uri::decode($value));
			}
		}
		$subject = $this->getCurrentInstance();
		
		if($this->service->setDeliveryGroups($subject, $groups)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
}
?>