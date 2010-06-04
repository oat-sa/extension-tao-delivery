<?php
// require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
// set_include_path(get_include_path().';'.dirname(__FILE__).'/../..');

require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';
require_once dirname(__FILE__) . '/../includes/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

if(!defined("LOGIN")){
	define("LOGIN", "generis", true);
}
/**
* @constant password for the module you wish to connect to 
*/
if(!defined("PASS")){
	define("PASS", "g3n3r1s", true);
}
/**
* @constant module for the module you wish to connect to 
*/
if(!defined("MODULE")){
	define("MODULE", "tao", true);
}

error_reporting(E_ALL);

class DeliveryTestCase extends UnitTestCase {
	
	protected $deliveryService = null;
	protected $delivery = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		// TestRunner::initTest();
		// $this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		// $this->apiModel->logIn(LOGIN,md5(PASS),DATABASE_NAME,true);
		// $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		// $processDefinition = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process authoring service');
		// if($processDefinition instanceof core_kernel_classes_Resource){
			// $this->proc = $processDefinition;
		// }
	}
	
	public function testService(){
		
		$deliveryService = tao_models_classes_ServiceFactory::get('Delivery');
		$this->assertIsA($deliveryService, 'tao_models_classes_Service');
		$this->assertIsA($deliveryService, 'taoDelivery_models_classes_DeliveryService');
		
		$this->deliveryService = $deliveryService;
	}
	
	public function testCreateInstance(){
		$delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery1');
		
		//check if a process is associated to the delivery content:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//check if the default delivery server exists:
		$defaultDeliveryServer = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
		$this->assertNotNull($defaultDeliveryServer);
		
		if(!is_null($defaultDeliveryServer)){
			$this->assertEqual( (string) $defaultDeliveryServer->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_URL_PROP)), 'localhost');
		}
		
		$this->delivery = $delivery;
		
		
	}
	
	public function testSetDeliveryTests(){
	
		//create 2 tests:
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testClass->createInstance('UnitDelivery Test1', 'Test 1 create for delivery unit test');
		$test2 = $testClass->createInstance('UnitDelivery Test2', 'Test 2 create for delivery unit test');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
		
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		
		//analyze the content of the process:
		$process = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//count the number of activity:
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		$activities = $authoringService->getActivitiesByProcess($process);
		$this->assertEqual(count($activities), 2);
		foreach($activities as $activity){
			//check if an interactive is set and that it is the test
			$service = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			$this->assertNotNull($service);
			if(!is_null($service)){
				$serviceDefinition = $service->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
				$formalParamCollection = $serviceDefinition->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN));
				$this->assertEqual($formalParamCollection->count(), 3);
			}
		}
		
		$test1->delete();
		$test2->delete();
	}
	
	public function testCompile(){
	
	}
	
	public function testDeleteDelivery(){
		$this->deliveryService->deleteDelivery($this->delivery);
		//check if the process has been deleted:
		$this->assertNull($this->delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT)));
	}
}

