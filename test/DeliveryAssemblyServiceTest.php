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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use \common_ext_ExtensionsManager;
use taoDelivery_models_classes_DeliveryAssemblyService;
use \taoDelivery_models_classes_DeliveryTemplateService;
use \taoTests_models_classes_TestsService;
use \core_kernel_classes_Class;
use \core_kernel_classes_Property;
use \core_kernel_classes_Resource;
use \common_report_Report;

class DeliveryAssemblyServiceTest extends TaoPhpUnitTestRunner
{

    private $assemblyService;

    private $deliveryTemplate;

    /**
     * tests initialization
     */
    public function setUp()
    {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        
        TaoPhpUnitTestRunner::initTest();
        $this->assemblyService = taoDelivery_models_classes_DeliveryAssemblyService::singleton();
        
        $testsService = taoTests_models_classes_TestsService::singleton();
        $this->test = $testsService->createInstance($testsService->getRootclass(), 'deliveryUnitCompilerTest');
        
        $deliveryContentSuperClass = new core_kernel_classes_Class(CLASS_ABSTRACT_DELIVERYCONTENT);
        
        $this->contentClass = $deliveryContentSuperClass->createSubClass('abstractContentSubclass');
        $this->content = $this->contentClass->createInstanceWithProperties(array(
            PROPERTY_DELIVERY_CONTENT => $this->test->getUri(),
            RDFS_LABEL => 'contentInstanceUnitTest'
        ));
    }

    protected function tearDown()
    {
        $this->contentClass->delete();
        $this->content->delete();
        $this->test->delete();
    }

    protected function getCompilerMock($resource, $storage)
    {
        $compilerMock = $this->getMockBuilder('taoDelivery_models_classes_DeliveryCompiler')
            ->setConstructorArgs(array(
            $resource,
            $storage
        ))
            ->setMethods(array(
            'compile',
            'getSpawnedDirectoryIds'
        ))
            ->setMockClassName('taoDelivery_models_classes_DeliveryCompiler_Mock')
            ->disableOriginalConstructor()
            ->getMock();
        
        $fakeServiceCall = $this->getMockBuilder('tao_models_classes_service_ServiceCall')
            ->setMethods(array(
            'toOntology'
        ))
            ->disableOriginalConstructor()
            ->getMock();
        
        $fakeServiceCall->expects($this->any())
            ->method('toOntology')
            ->will($this->returnValue(GENERIS_TRUE));
        
        $report = new common_report_Report(common_report_Report::TYPE_INFO);
        $report->setType(common_report_Report::TYPE_SUCCESS);
        $report->setMessage('Unit Test Report');
        $report->setData($fakeServiceCall);
        
        $compilerMock->expects($this->any())
            ->method('compile')
            ->will($this->returnValue($report));
        
        $compilerMock->expects($this->any())
            ->method('getSpawnedDirectoryIds')
            ->will($this->returnValue(array(
            'IdoNotExist'
        )));
        
        return $compilerMock;
    }

    /**
     * Create Assmebly
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return \common_report_Report
     */
    private function getAssembly(){
        $rootClass = $this->assemblyService->getRootClass();
        
        $storage = \tao_models_classes_service_FileStorage::singleton();
        
        $assemblyServiceMock = $this->getMockBuilder('taoDelivery_models_classes_DeliveryAssemblyService')
        ->setMethods(array(
            'getCompiler'
        ))
        ->disableOriginalConstructor()
        ->setMockClassName('taoDelivery_models_classes_DeliveryAssemblyService_Mock')
        ->getMock();
        
        $assemblyServiceMock->expects($this->any())
        ->method('getCompiler')
        ->with($this->content)
        ->will($this->returnValue($this->getCompilerMock($this->content, $storage)));
        
        $report = $assemblyServiceMock->createAssembly($rootClass, $this->content);
        return $report;
    }
    
    
    /**
     * create assembly from template
     */
    public function testCreateAssembly()
    {

        $report = $this->getAssembly();
        
        $this->assertInstanceOf('common_report_Report', $report);
        $this->assertEquals($report->getType(), common_report_Report::TYPE_SUCCESS);
        $assembly = $report->getData();
        $this->assertInstanceOf('core_kernel_classes_Resource', $assembly);
        
        $values = $assembly->getPropertiesValues(array(
            PROPERTY_COMPILEDDELIVERY_DIRECTORY,
            PROPERTY_COMPILEDDELIVERY_TIME,
            PROPERTY_COMPILEDDELIVERY_RUNTIME
        ));
        
        $this->assertInstanceOf('core_kernel_classes_Literal', current($values[PROPERTY_COMPILEDDELIVERY_DIRECTORY]));
        $this->assertEquals('IdoNotExist', current($values[PROPERTY_COMPILEDDELIVERY_DIRECTORY]));
        $this->assertInstanceOf('core_kernel_classes_Literal', current($values[PROPERTY_COMPILEDDELIVERY_TIME]));
        $this->assertGreaterThanOrEqual(time(), intval(current($values[PROPERTY_COMPILEDDELIVERY_TIME])->literal));
        $this->assertInstanceOf('core_kernel_classes_Resource', current($values[PROPERTY_COMPILEDDELIVERY_RUNTIME]));
        $this->assertEquals(GENERIS_TRUE, current($values[PROPERTY_COMPILEDDELIVERY_RUNTIME])->getUri());
        
        $assembly->delete();
    }
    
    /**
     * Create Assembly from template
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param \core_kernel_classes_Resource $deliveryTemplate
     * @return common_report_Report
     */
    private function getAssemblyFromTemplate($deliveryTemplate){
        $storage = \tao_models_classes_service_FileStorage::singleton();
        
        $assemblyServiceMock = $this->getMockBuilder('taoDelivery_models_classes_DeliveryAssemblyService')
        ->setMethods(array(
            'getCompiler'
        ))
        ->disableOriginalConstructor()
        ->setMockClassName('taoDelivery_models_classes_DeliveryAssemblyService_Mock')
        ->getMock();
        
        $assemblyServiceMock->expects($this->any())
        ->method('getCompiler')
        ->will($this->returnValue($this->getCompilerMock($this->content, $storage)));
        
        $report = $assemblyServiceMock->createAssemblyFromTemplate($deliveryTemplate);
        return $report;
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetAssembliesByTemplate(){
        $templateService = taoDelivery_models_classes_DeliveryTemplateService::singleton();
        $deliveryTemplate = $templateService->createInstance($templateService->getRootClass(), 'unit test delivery template');
        $deliveryTemplate->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERY_CONTENT), $this->content);
        
        $report = $this->getAssemblyFromTemplate($deliveryTemplate);
        $assembly = $report->getData();
        
        $res = $this->assemblyService->getAssembliesByTemplate($deliveryTemplate,true);
        $this->assertInstanceOf('core_kernel_classes_Resource', current($res));       
        $this->assertEquals($assembly->getUri(),current($res)->getUri());
        
        $deliveryTemplate->delete();
        $assembly->delete();
    }
    
}
