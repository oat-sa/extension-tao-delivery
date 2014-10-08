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
 *               2013-2014 (update and modification) Open Assessment Technologies SA  
 */

namespace oat\taoDelivery\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use \taoDelivery_models_classes_DeliveryTemplateService;
use \taoDelivery_models_classes_DeliveryCompiler;
use \core_kernel_classes_Class;
use \core_kernel_classes_Property;
use \common_ext_ExtensionsManager;
use \tao_models_classes_service_FileStorage;
use \common_report_Report;
use \taoTests_models_classes_TestsService;

class DeliveryCompilerTest extends TaoPhpUnitTestRunner {

    /**
     * @var taoDelivery_models_classes_DeliveryTemplateService
     */
	protected $deliveryService = null;

    protected $delivery = null;
    protected $contentClass = null;
    protected $content = null;
    protected $test = null;

    static public function samplesDir() {
        return dirname(__FILE__) . '/data/';
    }

	/**
	 * tests initialization
	 */
	public function setUp() {
	    common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
	     
        TaoPhpUnitTestRunner::initTest();
		$this->deliveryService = taoDelivery_models_classes_DeliveryTemplateService::singleton();
 		$rootClass = $this->deliveryService->getRootClass();
		
 		$this->delivery =  $rootClass->createInstance('deliveryUnitCompilerTest');
 		$testsService = taoTests_models_classes_TestsService::singleton();
 		$this->test = $testsService->createInstance($testsService->getRootclass(), 'deliveryUnitCompilerTest');
        
 		$deliveryContentSuperClass = new core_kernel_classes_Class(CLASS_ABSTRACT_DELIVERYCONTENT);
 			
 		$this->contentClass = $deliveryContentSuperClass->createSubClass('abstractContentSubclass');
 		$this->content = $this->contentClass->createInstanceWithProperties(array(
 		    PROPERTY_DELIVERY_CONTENT => $this->test->getUri(),
 		    RDFS_LABEL => 'contentInstanceUnitTest'
 		));
	}


	

	protected function tearDown() {
 	    $this->delivery->delete();
 	    $this->contentClass->delete();
 	    $this->content->delete();
 	    $this->test->delete();

	}

	/**
	 * This mock is currently not use we need a service manager to create mock content 
	 * 
	 * @author Lionel Lecaque, lionel@taotesting.com
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
    protected function getContentModelMock(){

        $contentModelMock =$this->getMockForAbstractClass(
            'taoDelivery_models_classes_ContentModel', 
            [],
            'taoDelivery_models_classes_ContentModel_Mock',
            false, 
            false, 
            true, 
            ['getCompilerClass']);
        
        $contentModelMock
            ->expects($this->any())
            ->method('getCompilerClass')
            ->will($this->returnValue('taoDelivery_models_classes_DeliveryCompiler_Mock'));
        

        return $contentModelMock;
         
    }

    /**
     * Without a service manager getContentMock could not be use to test createCompiler 
     * because the class is instanciate on the fly, should be replace with proper test
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCompilerMock($resource,$storage){
        
        
         $compilerMock = $this->getMockBuilder('taoDelivery_models_classes_DeliveryCompiler')
                              ->setConstructorArgs(array($resource,$storage))
                              ->setMethods( array('compile'))
                              ->setMockClassName('taoDelivery_models_classes_DeliveryCompiler_Mock')
                              ->disableOriginalConstructor()
                              ->getMock();
         
        $report = new common_report_Report(common_report_Report::TYPE_INFO);
        $report->setType(common_report_Report::TYPE_SUCCESS);
        $report->setMessage('Unit Test Report');
         
        $compilerMock
             ->expects($this->any())
             ->method('compile')
             ->will($this->returnValue($report));
         
         return $compilerMock;
    }

    /**
     * Check if the delivery server exists
     * @return \taoDelivery_models_classes_DeliveryCompiler
     */
    public function testCreateCompiler() {
        $storage = tao_models_classes_service_FileStorage::singleton();
        
        $deliveryCompiler = $this->getCompilerMock($this->delivery,$storage);
        // When serviceManager will be implemented we will have to replace the mock with this
        // $implProp = new core_kernel_classes_Property(PROPERTY_CONTENTCLASS_IMPLEMENTATION);
        // $contentModelMock = $this->getContentModelMock();
        // $className = get_class($contentModelMock);
        // $this->contentClass->setPropertyValue($implProp, get_class($contentModelMock));
        // $deliveryCompiler = taoDelivery_models_classes_DeliveryCompiler::createCompiler($this->content);
        
        $this->assertInstanceOf('taoDelivery_models_classes_DeliveryCompiler', $deliveryCompiler);
 
        return $deliveryCompiler;
    }
    /**
     * Check if the delivery server exists
     * @return \taoDelivery_models_classes_DeliveryCompiler
     */
    public function testCompile() {
        $storage = tao_models_classes_service_FileStorage::singleton();
        $deliveryCompiler = $this->getCompilerMock($this->delivery,$storage);
                
        $report = $deliveryCompiler->compile();
        $this->assertInstanceOf('common_report_Report', $report);
        $this->assertEquals(common_report_Report::TYPE_SUCCESS, $report->getType());
        
    }



}
