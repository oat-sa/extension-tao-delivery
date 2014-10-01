<?php
/**  
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
namespace oat\taoTestTaker\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use \taoDelivery_models_classes_DeliveryTemplateService;
use \core_kernel_classes_Class;
use \core_kernel_classes_Property;
use \common_ext_ExtensionsManager;
use \taoResultServer_models_classes_ResultServerAuthoringService;

class DeliveryServiceTest extends TaoPhpUnitTestRunner {

    /**
     * @var taoDelivery_models_classes_DeliveryTemplateService
     */
	protected $deliveryService = null;

	/**
	 * tests initialization
	 */
	public function setUp() {
	    common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        TaoPhpUnitTestRunner::initTest();
		$this->deliveryService = taoDelivery_models_classes_DeliveryTemplateService::singleton();
	}

    /**
     * verify delivery class
     * @return void
     */
	public function testService() {
		$this->assertIsA($this->deliveryService, 'taoDelivery_models_classes_DeliveryTemplateService');
	}

    /**
     * verify delivery class
     * @return \core_kernel_classes_Resource
     */
	public function testCreateInstance() {
	    
		$delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery2');
		$this->assertInstanceOf( 'core_kernel_classes_Resource', $delivery);
		$delivyType = current($delivery->getTypes());
		$this->assertEquals(TAO_DELIVERY_CLASS, $delivyType->getUri());
        return $delivery;
	}

    /**
     * Check if the delivery server exists
     * @depends testCreateInstance
     * @param $delivery
     * @return void
     */
    public function testDeliveryServer($delivery) {
		$deliveryServer = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
		$this->assertNotNull($deliveryServer);

        return $deliveryServer;
    }

    /**
     * Verify the delivery server is the same as default server
     * @depends testDeliveryServer
     * @param $deliveryServer
     * @return void
     */
    public function testVerifyDeliveryServer($deliveryServer) {
        $defaultDeliveryServer = taoResultServer_models_classes_ResultServerAuthoringService::singleton()->getDefaultResultServer();
        $this->assertEquals($defaultDeliveryServer->getUri(), $deliveryServer->getUri());
    }

    /**
     * Delete delivery instance
     * @depends testCreateInstance
     * @param $delivery
     */
    public function testDeleteInstance($delivery) {
		$this->deliveryService->deleteInstance($delivery);
		$this->assertFalse($delivery->exists());
    }

    /**
     * Verify delivery instance deletion
     * @depends testCreateInstance
     * @param $delivery
     */
    public function testVerifyInstanceDeletion($delivery) {
		$this->assertNull($delivery->getOnePropertyValue(new core_kernel_classes_Property(RDF_TYPE)));
    }

    /**
     * Create SubClass
     * @return \core_kernel_classes_Class
     */
	public function testCreateClass() {
	    $deliveryClass = $this->deliveryService->createSubClass($this->deliveryService->getRootClass(), 'UnitTestDeliveryClass');
	    $this->assertInstanceOf( 'core_kernel_classes_Class',$deliveryClass);
	    $subclass = $deliveryClass->getOnePropertyValue(new core_kernel_classes_Property(RDFS_SUBCLASSOF));
	    $this->assertEquals(CLASS_DELIVERY_TEMPLATE, $subclass->getUri());
        return $deliveryClass;
    }

    /**
     * Verify that just created delivery class exists
     * @depends testCreateClass
     * @param $deliveryClass
     * @return void
     */
	public function testDeliveryClassExists($deliveryClass) {
		$this->assertTrue($deliveryClass->exists());
    }

    /**
     * Verify that just created delivery class exists
     * @depends testCreateClass
     * @param $deliveryClass
     * @return void
     */
	public function testCreateDeliveryInstance($deliveryClass) {
	    $deliveryInstance = $this->deliveryService->createInstance($deliveryClass, 'UnitTestDelivery3');
		$this->assertInstanceOf('core_kernel_classes_Resource',$deliveryInstance);

        return $deliveryInstance;
    }

    /**
     * Verify that just created delivery instance exists
     * @depends testCreateDeliveryInstance
     * @param $deliveryInstance
     * @return void
     */
	public function testDeliveryInstanceExists($deliveryInstance) {
		$this->assertTrue($deliveryInstance->exists());
    }

    /**
     * Verify tye number of types of deliveryInstance
     * @depends testCreateDeliveryInstance
     * @param $deliveryInstance
     * @return void
     */
	public function testDeliveryInstanceTypes($deliveryInstance) {
		$this->assertEquals(1, count($deliveryInstance->getTypes()));
    }

    /**
     * Verify deliveryInstance is an instance of deliveryClass
     * @depends testCreateDeliveryInstance
     * @depends testCreateClass
     * @param $deliveryInstance
     * @param $deliveryClass
     * @return void
     */
	public function testVerifyDeliveryInstance($deliveryInstance, $deliveryClass) {
        $this->assertTrue($deliveryInstance->isInstanceOf($deliveryClass));
    }

    /**
     * Delete delivery instance
     * @depends testCreateDeliveryInstance
     * @param $deliveryInstance
     */
    public function testDeleteDeliveryInstance($deliveryInstance) {
        $this->deliveryService->deleteInstance($deliveryInstance);
        $this->assertFalse($deliveryInstance->exists());
    }

    /**
     * Delete delivery class
     * @depends testCreateClass
     * @param $deliveryClass
     */
    public function testDeleteDeliveryClass($deliveryClass) {
        $this->deliveryService->deleteDeliveryClass($deliveryClass);
        $this->assertFalse($deliveryClass->exists());
    }

}