<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

error_reporting(E_ALL);

class DeliveryTestCase extends UnitTestCase {
	
	protected $deliveryService = null;
	protected $delivery = null;
	protected $authoringService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
		
		$this->authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		$this->deliveryService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryService');
		
		// $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery');
		if($delivery instanceof core_kernel_classes_Resource){
			$this->delivery = $delivery;
		}
	}
	
	public function tearDown() {
	   $this->deliveryService->deleteDelivery($this->delivery);
    }
	
	
	public function testService(){
		$deliveryService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryService');
		$this->assertIsA($deliveryService, 'taoDelivery_models_classes_DeliveryService');
	}
	
	public function testCreateInstance(){
		$delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery2');
		
		//check if a process is associated to the delivery content:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//check if the default delivery server exists:
		$defaultDeliveryServer = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
		$this->assertNotNull($defaultDeliveryServer);
		
		if(!is_null($defaultDeliveryServer)){
			$this->assertEqual($defaultDeliveryServer->uriResource, TAO_DELIVERY_DEFAULT_RESULT_SERVER);
		}
		
		$this->deliveryService->deleteDelivery($delivery);
		$this->assertNull($delivery->getOnePropertyValue(new core_kernel_classes_Property(RDF_TYPE)));
	}

	public function testSetDeliveryTests(){
	
		//create 2 tests:
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testClass->createInstance('UnitDelivery Test1', 'Test 1 create for delivery unit test');
		$test2 = $testClass->createInstance('UnitDelivery Test2', 'Test 2 create for delivery unit test');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
		
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		
		// analyze the content of the process:
		$process = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		// count the number of activity:
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		$activities = $authoringService->getActivitiesByProcess($process);
		$this->assertEqual(count($activities), 2);
		
		foreach($activities as $activity){//check if an interactive is set and that it is the test
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
		$this->assertIsA($test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP)), 'core_kernel_classes_Resource');
		
		//set item 1 and 2 to test 1 and items 3 and 4 to test 2
		$this->assertTrue($testsService->setTestItems($test1, array($item1, $item2)));
		$this->assertTrue($testsService->setTestItems($test2, array($item3, $item4)));
		
		//set the 2 tests to the delivery
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		$this->assertEqual(count($this->deliveryService->getDeliveryTests($this->delivery)), 2);
		
		//generate the actual delivery process:
		$generationResult = $this->deliveryService->generateProcess($this->delivery);
		$this->assertTrue($generationResult['success']);
		$deliveryProcess = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_PROCESS));
		
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		$this->assertEqual(count($authoringService->getActivitiesByProcess($deliveryProcess)), 4);//there should be 4 activities (i.e. items)
	
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$testsService->deleteTest($test1);
		$testsService->deleteTest($test2);
	}
	
	
	public function testGenerateProcessConditionalTest(){
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
		
		$this->assertIsA($test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP)), 'core_kernel_classes_Resource');
		
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
		$generationResult = $this->deliveryService->generateProcess($this->delivery);
		$this->assertTrue($generationResult['success']);$deliveryProcess = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_PROCESS));
				
		$this->assertEqual(count($authoringService->getActivitiesByProcess($deliveryProcess)), 4);//there should be 4 activities (i.e. items)
		
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$testsService->deleteTest($test1);
		$testsService->deleteTest($test2);
	}
	
	
	public function testGenerateProcessConditionalDelivery(){
		$prefix_item = "!item: UnitCondDelivery ";
		$prefix_test = "!test: UnitCondDelivery ";
		
		//create 2 tests with 2 items:
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$item1 = $itemClass->createInstance('UnitDelivery Item1', 'Item 1 created for delivery unit test');
		$item2 = $itemClass->createInstance('UnitDelivery Item2', 'Item 2 created for delivery unit test');
		$item3 = $itemClass->createInstance('UnitDelivery Item3', 'Item 3 created for delivery unit test');
		$item4 = $itemClass->createInstance('UnitDelivery Item4', 'Item 4 created for delivery unit test');
		$item5 = $itemClass->createInstance('UnitDelivery Item5', 'Item 5 created for delivery unit test');
		
		//create required test authoring:
		$testsService = tao_models_classes_ServiceFactory::get('Tests');
		$this->assertIsA($testsService, 'tao_models_classes_GenerisService');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		
		//create 2 test instances with the tests service (to initialize the test processes)
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testsService->createInstance($testClass, 'UnitDelivery Test1');
		$test2 = $testsService->createInstance($testClass, 'UnitDelivery Test2');
		$test3 = $testsService->createInstance($testClass, 'UnitDelivery Test3');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
			
		$this->assertIsA($test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP)), 'core_kernel_classes_Resource');
		
		//init authoring service:
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		//set item 1 and 2 to test 1 and items 3 and 4 to test 2
		$processTest1 = $test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		
		
		$activityItem1 = $authoringService->createActivity($processTest1, "{$prefix_item}Item_1");
		$activityItem1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connectorItem1 = $authoringService->createConnector($activityItem1);
		
		$activityItem2 = $authoringService->createSplitActivity($connectorItem1, 'then', null, "{$prefix_item}Item_2");//create actiivty for item 2:
		$activityItem3 = $authoringService->createSplitActivity($connectorItem1, 'else', null, "{$prefix_item}Item_3");
		
		//processTest2 and 3 are sequential:
		$this->assertTrue($testsService->setTestItems($test2, array($item4)));
		$this->assertTrue($testsService->setTestItems($test3, array($item5)));
		
		//set the 3 tests in a conditional delivery:
		$processDelivery = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		$activityTest1 = $authoringService->createActivity($processDelivery, "{$prefix_test}Test_1");
		$activityTest1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connectorTest1 = $authoringService->createConnector($activityTest1);
		$activityTest2 = $authoringService->createSplitActivity($connectorTest1, 'then', null, "{$prefix_test}Test_2");//create actiivty for item 2:
		$activityTest3 = $authoringService->createSplitActivity($connectorTest1, 'else', null, "{$prefix_test}Test_3");
		
		$interactiveService = $authoringService->setTestByActivity($activityTest1, $test1);
		$this->assertNotNull($interactiveService);
		$interactiveService = $authoringService->setTestByActivity($activityTest2, $test2);
		$interactiveService = $authoringService->setTestByActivity($activityTest3, $test3);
				
		//generate the actual delivery process:
		$generationResult = $this->deliveryService->generateProcess($this->delivery);
		$this->assertTrue($generationResult['success']);
		
		$deliveryProcess = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_PROCESS));
		$this->assertIsA($deliveryProcess, 'core_kernel_classes_Resource');
		$deliveryProcessChecker = new wfEngine_models_classes_ProcessChecker($deliveryProcess);
		$this->assertTrue($deliveryProcessChecker->checkProcess());
		
		
		$this->assertEqual(count($authoringService->getActivitiesByProcess($deliveryProcess)), 5);
	
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$item5->delete();
		$testsService->deleteTest($test1);
		$testsService->deleteTest($test2);
		$testsService->deleteTest($test3);
	}
	
}

