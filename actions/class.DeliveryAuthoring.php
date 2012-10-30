<?php
/**
 * DeliveryAuthoring Controller provide actions to edit a delivery
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class taoDelivery_actions_DeliveryAuthoring extends wfAuthoring_actions_ProcessAuthoring {
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new taoDelivery_models_classes_DeliveryAuthoringService();
		$this->defaultData();
	}
	
	/**
     * Get json encoded array of tests data to populate the tests tree
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
	public function getTestData(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$tests = $this->service->toTree( new core_kernel_classes_Class(TAO_TEST_CLASS), array());
		// throw new Exception(var_dump($tests));
		$reformatedTreeData =array(
			'data' => $tests['data'],
			'attributes' => $tests['attributes']
		);
		
		if(isset($tests['children']) && !empty($tests['children'])){
			$reformatedTreeData['children'] = $this->reformatTestData($tests['children']);
		}
		
		echo json_encode($reformatedTreeData);
	}
	
	/**
     * Reformat the tests tree data, by adding the information on the compiled test url 
	 *
     * @access private
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param array tests
     * @return array
     */
	private function reformatTestData($tests){
		$instancesData = array();
		foreach($tests as $test){
			if($test['attributes']['class'] == 'node-instance'){
				// $testId = tao_helpers_Uri::getUniqueId( tao_helpers_Uri::decode($test['attributes']['id']) );
				$testUrl = taoDelivery_helpers_Compilator::getCompiledTestUrl(tao_helpers_Uri::decode($test['attributes']['id']));
				if(!empty($testUrl)){
					$test['attributes']['val'] = $testUrl;//BASE_URL."/compiled/{$testId}/theTest.php?subject=^subjectUri&wsdl=^wsdlContract";	
				}
				
			}elseif($test['attributes']['class'] == 'node-class'){
				if(isset($test['children']) && !empty($test['children'])){
					$test['children'] = $this->reformatTestData($test['children']);
				}
			}
			$instancesData[] = $test;
		}
		return $instancesData;
	}
	
	/**
     * Get an array of tests composing a delivery
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param  core_kernel_classes_Resource delivery
     * @return array
     */
	public function getDeliveryTests(core_kernel_classes_Resource $delivery){
		$returnValue = array();
		
		if(!is_null($delivery)){
			$returnValue = $this->getProcessTests($delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT)));
		}
		
		return $returnValue;
	}
	
	/**
     * Get an array of tests composing a process
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource process
     * @return array
     */
	public function getProcessTests(core_kernel_classes_Resource $process){
	
		if(is_null($process)){
			$process = $this->getCurrentProcess();
		}
		
		$activities = $this->service->getActivitiesByProcess($process);
		$tests = array();
		
		foreach($activities as $activity){
			$test = $this->service->getTestByActivity($activity);
			
			if(!is_null($test) && $test instanceof core_kernel_classes_Resource){
				$tests[$test->uriResource] = $test;
			}
		}
		
		return $tests;
	}
	
	/**
	 * get the compilation view
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function compileView(){
	
		$currentProcess = $this->getCurrentProcess();
		if(!is_null($currentProcess)){
			
			$currentDelivery = $this->service->getDeliveryContentFromProcess($currentProcess);
			if(is_null($currentDelivery)){
				throw new Exception("no delivery found for the current process");
			}
			
			$this->setData("processUri", urlencode($currentDelivery->uriResource));
			$this->setData("processLabel", $currentDelivery->getLabel());
			foreach($currentDelivery->getType() as $deliveryClass){
				$this->setData("deliveryClass", tao_helpers_Uri::encode($deliveryClass->uriResource));
				break;
			}
			//compilation state:
			$deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
			$isCompiled=$deliveryService->isCompiled($currentDelivery);
			$this->setData("isCompiled", $isCompiled);
			if($isCompiled){
				$compiledDate = $currentDelivery->getLastModificationDate(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP));
				$this->setData("compiledDate", $compiledDate->format('d/m/Y H:i:s'));
			}
		}
		
		$this->setView("process_compiling.tpl");
	}
	
}
?>