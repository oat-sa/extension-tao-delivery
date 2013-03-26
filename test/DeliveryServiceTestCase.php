<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class DeliveryServiceTestCase extends UnitTestCase {
	
	protected $deliveryService = null;
	protected $delivery = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
		
		$this->deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
		
		// $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$this->delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery');
	}
	
	public function tearDown() {
	   $this->deliveryService->deleteDelivery($this->delivery);
    }
	
	
	public function testService(){
		$this->assertIsA($this->delivery, 'core_kernel_classes_Resource');
		$this->assertIsA($this->deliveryService, 'taoDelivery_models_classes_DeliveryService');
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
		$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
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

	public function testAuthoringMode() {
		
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testClass->createInstance('UnitDelivery Test1', 'Test 1 create for delivery unit test');
		$test2 = $testClass->createInstance('UnitDelivery Test2', 'Test 2 create for delivery unit test');
		$testKeys = array($test1->getUri(), $test2->getUri());
		
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		$tests = $this->deliveryService->getDeliveryTests($this->delivery);
		$this->assertTrue($test1->equals(array_shift($tests)));
		$this->assertTrue($test2->equals(array_shift($tests)));
		$this->assertTrue(empty($tests));
		
		$this->assertTrue($this->deliveryService->setAuthoringMode($this->delivery, 'advanced'));
		$tests = $this->deliveryService->getDeliveryTests($this->delivery);
		$this->assertTrue($test1->equals(array_shift($tests)));
		$this->assertTrue($test2->equals(array_shift($tests)));
		$this->assertTrue(empty($tests));
						
		$this->assertTrue($this->deliveryService->setAuthoringMode($this->delivery, 'simple'));
		$tests = $this->deliveryService->getDeliveryTests($this->delivery);
		$this->assertTrue($test1->equals(array_shift($tests)));
		$this->assertTrue($test2->equals(array_shift($tests)));
		$this->assertTrue(empty($tests));
				
		$test1->delete();
		$test2->delete();
	}
}

