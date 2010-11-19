<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/common.php';

error_reporting(E_ALL);

class DeliveryTestCase extends UnitTestCase {
	
	protected $deliveryService = null;
	protected $delivery = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
	}
	
	public function testService(){
		
		$deliveryService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryService');
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
			$this->assertEqual($defaultDeliveryServer->uriResource, TAO_DELIVERY_DEFAULT_RESULT_SERVER);
		}
		
		$this->delivery = $delivery;
		
	}
	/*
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
				$this->assertEqual($formalParamCollection->count(), 1);//the testUri param only
			}
		}
		
		$test1->delete();
		$test2->delete();
	}
	*/
	/*
	public function testGenerateProcess(){
		//create 2 tests with 2 items:
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$item1 = $itemClass->createInstance('UnitDelivery Item1', 'Item 1 created for delivery unit test');
		$item2 = $itemClass->createInstance('UnitDelivery Item2', 'Item 2 created for delivery unit test');
		$item3 = $itemClass->createInstance('UnitDelivery Item3', 'Item 3 created for delivery unit test');
		$item4 = $itemClass->createInstance('UnitDelivery Item4', 'Item 4 created for delivery unit test');
		
		//create required test authoring:
		$testsService = tao_models_classes_ServiceFactory::get('Tests');
		$this->assertIsA($testsService, 'tao_models_classes_GenerisService');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		
		//create 2 test instances with the tests service (to initialize the test processes)
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testsService->createInstance($testClass, 'UnitDelivery Test1');
		$test2 = $testsService->createInstance($testClass, 'UnitDelivery Test2');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
		$this->assertIsA($test2, 'core_kernel_classes_Resource');
		$this->assertIsA($test1->getUniquePropertyValue(new core_kernel_classes_Class(TEST_TESTCONTENT_PROP)), 'core_kernel_classes_Resource');
		
		//set item 1 and 2 to test 1 and items 3 and 4 to test 2
		$this->assertTrue($testsService->setTestItems($test1, array($item1, $item2)));
		$this->assertTrue($testsService->setTestItems($test2, array($item3, $item4)));
		
		//set the 2 tests to the delivery
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		$this->assertEqual(count($this->deliveryService->getDeliveryTests($this->delivery)), 2);
		
		//generate the actual delivery process:
		$deliveryProcess = $this->deliveryService->generateProcess($this->delivery);
		$this->assertIsA($deliveryProcess, 'core_kernel_classes_Resource');
		
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		$this->assertEqual(count($authoringService->getActivitiesByProcess($deliveryProcess)), 4);//there should be 4 activities (i.e. items)
	
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$testsService->deleteTest($test1);
		$testsService->deleteTest($test2);
	}
	*/
	
	public function testGenerateProcessConditional(){
		$id = "!item: UnitDelivery ";
		
		
		//create 2 tests with 2 items:
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$item1 = $itemClass->createInstance('UnitDelivery Item1', 'Item 1 created for delivery unit test');
		$item2 = $itemClass->createInstance('UnitDelivery Item2', 'Item 2 created for delivery unit test');
		$item3 = $itemClass->createInstance('UnitDelivery Item3', 'Item 3 created for delivery unit test');
		$item4 = $itemClass->createInstance('UnitDelivery Item4', 'Item 4 created for delivery unit test');
		
		//create required test authoring:
		$testsService = tao_models_classes_ServiceFactory::get('Tests');
		$this->assertIsA($testsService, 'tao_models_classes_GenerisService');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		
		//create 2 test instances with the tests service (to initialize the test processes)
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testsService->createInstance($testClass, 'UnitDelivery Test1');
		$test2 = $testsService->createInstance($testClass, 'UnitDelivery Test2');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
		$this->assertIsA($test2, 'core_kernel_classes_Resource');
		$this->assertIsA($test1->getUniquePropertyValue(new core_kernel_classes_Class(TEST_TESTCONTENT_PROP)), 'core_kernel_classes_Resource');
		
		//set item 1 and 2 to test 1 and items 3 and 4 to test 2
		$processTest1 = $test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
				
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		$activityItem1 = $authoringService->createActivity($processTest1, "{$id}Item_1");
		$activityItem1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connectorItem1 = $authoringService->createConnector($activityItem1);
		
		$activityItem2 = $authoringService->createSplitActivity($connectorItem1, 'then', null, "{$id}Item_2");//create actiivty for item 2:
		$activityItem3 = $authoringService->createSplitActivity($connectorItem1, 'else', null, "{$id}Item_3");
		
		//processTest2 is sequential:
		$this->assertTrue($testsService->setTestItems($test2, array($item4)));
		
		//set the 2 tests to the delivery sequentially:
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		$this->assertEqual(count($this->deliveryService->getDeliveryTests($this->delivery)), 2);
		
		//generate the actual delivery process:
		
		$deliveryProcess = $this->deliveryService->generateProcess($this->delivery);
		$this->assertIsA($deliveryProcess, 'core_kernel_classes_Resource');
		
		
		$this->assertEqual(count($authoringService->getActivitiesByProcess($deliveryProcess)), 4);//there should be 4 activities (i.e. items)
	
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$testsService->deleteTest($test1);
		$testsService->deleteTest($test2);
	}
	/**/
	
	/*
	public function testCompileTest(){
		// require_once(ROOT_PATH . '/taoTests/includes/constants.php');
		$testContentRefPath = ROOT_PATH .'/taoTests/data/test_content_ref.xml';
		// var_dump($testContentRefPath, TEST_CONTENT_REF_FILE);
		$this->assertTrue(file_exists($testContentRefPath));
		define('TEST_CONTENT_REF_FILE', $testContentRefPath);//explicitely define here, because required in testsService:
		define('TAO_ITEM_CONTENT_PROPERTY', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent');
					
		//create a full delivery, with real tests and items:
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$item1 = $itemClass->createInstance('UnitDelivery Item1', 'Item 1 create for delivery unit test');
		$item2 = $itemClass->createInstance('UnitDelivery Item2', 'Item 2 create for delivery unit test');
		$item3 = $itemClass->createInstance('UnitDelivery Item3', 'Item 3 create for delivery unit test');
		$item4 = $itemClass->createInstance('UnitDelivery Item4', 'Item 4 create for delivery unit test');
		
		$itemContentProp = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		$item1->editPropertyValues($itemContentProp, $xmlCTest);				
		$item2->editPropertyValues($itemContentProp, $xmlQCM);//$xmlQCM problem with img file copying
		$item3->editPropertyValues($itemContentProp, $xmlKohs);
		$item4->editPropertyValues($itemContentProp, $xmlHAWAI);
		
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testClass->createInstance('UnitDelivery Test1', 'Test 1 create for delivery unit test');
		
		//assign the items to the tests: 
		// $itemService = tao_models_classes_ServiceFactory::get('Items');
		// $this->assertIsA($itemsService, 'tao_models_classes_Service');
		// $this->assertIsA($itemsService, 'taoItems_models_classes_ItemsService');
		$testsService = tao_models_classes_ServiceFactory::get('Tests');
		$this->assertIsA($testsService, 'tao_models_classes_Service');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		$testsService->setRelatedItems($test1, array($item1->uriResource, $item2->uriResource, $item3->uriResource), true);
		
		//set tests to active to allow compilation:
		$test1->editPropertyValues(new core_kernel_classes_Property(TEST_ACTIVE_PROP), GENERIS_TRUE);
		
		//execute compilation:
		$results = $this->deliveryService->compileTest($test1->uriResource);
		$this->assertEqual($results['success'], 1);
		if($results['success'] != 1){
			var_dump($results);
		}
		
		//check create folder and files:
		$compilator = new taoDelivery_helpers_Compilator($test1->uriResource);
		$compiledPath = $compilator->getCompiledPath();
		// var_dump($compiledPath);
		$this->assertTrue(is_dir($compiledPath));
		$this->assertTrue(file_exists($compiledPath.'Test.xml'));
		$this->assertTrue(file_exists($compiledPath.'Test.swf'));
		$this->assertTrue(file_exists($compiledPath.'theTest.php'));
		
		//destroy compile folder contents:
		$this->assertTrue($compilator->clearCompiledFolder());
		$this->assertFalse(file_exists($compiledPath.'Test.xml'));
		
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$test1->delete();
	}
	*/
	
	public function testDeleteDelivery(){
		$this->deliveryService->deleteDelivery($this->delivery);
		$this->assertNull($this->delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT)));
		$this->assertNull($this->delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_PROCESS)));
	}
}

