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

include_once dirname(__FILE__) . '/../includes/raw_start.php';

class DeliveryCompilerTest extends TaoPhpUnitTestRunner {

    /**
     * @var taoDelivery_models_classes_DeliveryTemplateService
     */
	protected $deliveryService = null;

    /**
     * @var taoDelivery_models_classes_DeliveryAssemblyService
     */
	protected $assemblyService = null;

    static public function samplesDir() {
        return dirname(__FILE__) . '/data/';
    }

	/**
	 * tests initialization
	 */
	public function setUp() {
        TaoPhpUnitTestRunner::initTest();
		$this->deliveryService = taoDelivery_models_classes_DeliveryTemplateService::singleton();
        $this->assemblyService = taoDelivery_models_classes_DeliveryAssemblyService::singleton();
	}

    /**
     * create delivery instance
     * @return \core_kernel_classes_Resource
     */
    public function testCreateDeliveryInstance() {
        $delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery');
        $this->assertInstanceOf('core_kernel_classes_Resource', $delivery);

        return $delivery;
    }

    /**
     * delivery getContent
     * @depends testCreateDeliveryInstance
     * @param $delivery
     * @return void
     */
    public function testGetContent($delivery) {
        $content = $this->deliveryService->getContent($delivery);
        $this->assertInstanceOf('core_kernel_classes_Container', $content);

        return $content;
    }

    /**
     * Check if the delivery server exists
     * @depends testCreateDeliveryInstance
     * @param $delivery
     * @return \taoDelivery_models_classes_DeliveryCompiler
     */
    public function testCreateCompiler($delivery) {
        $content = $this->deliveryService->getContent($delivery);
        $storage = tao_models_classes_service_FileStorage::singleton();

        $stub = $this->getMockForAbstractClass('taoDelivery_models_classes_DeliveryCompiler', array($delivery, $storage));
        $deliveryCompiler = $stub::createCompiler($content);

        $this->assertInstanceOf('taoDelivery_models_classes_DeliveryCompiler', $deliveryCompiler);

        return $deliveryCompiler;
    }

    /**
     * check delivery compilier compile
     * @depends testCreateCompiler
     * @param $deliveryCompiler
     * @expectedException \taoDelivery_models_classes_EmptyDeliveryException
     */
    public function testCompilerCompile($deliveryCompiler) {
        $deliveryCompiler->compile();
    }

    /**
     * create delivery instance
     * @return \core_kernel_classes_Resource
     */
	public function testCreateInstance() {
		$delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery');
		$this->assertInstanceOf( 'core_kernel_classes_Resource', $delivery);
		$delivyType = current($delivery->getTypes());
		$this->assertEquals(TAO_DELIVERY_CLASS, $delivyType->getUri());
        return $delivery;
	}

    /**
     * create assembly from template
     * @depends testCreateInstance
     * @param $delivery
     * @return \common_report_Report
     */
    public function testCreateAssemblyService($delivery) {
        $report = $this->assemblyService->createAssemblyFromTemplate($delivery);

        $this->assertInstanceOf('common_report_Report', $report);
        $this->assertEquals($report->getType(), common_report_Report::TYPE_ERROR);
	    $this->assertNull($report->getData());

        return $report;
    }
}
