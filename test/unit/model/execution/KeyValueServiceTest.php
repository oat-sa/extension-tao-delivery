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
namespace oat\taoDelivery\test\unit;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\KVDeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\model\execution\Service as ExecutionService;

class KeyValueServiceTest extends TaoPhpUnitTestRunner
{
    public function testSetState()
    {
        $service = $this->getKvService();
        $this->assertInstanceOf(ExecutionService::class, $service);
        
        $assembly = new \core_kernel_classes_Resource('fake');
        $deWrapper = $service->spawnDeliveryExecution('DE label', $assembly, 'fakeUser', 'http://uri.com/fake#StartState');
        
        $this->assertInstanceOf(DeliveryExecution::class, $deWrapper);
        $deliveryExecution = $deWrapper->getImplementation();
        $this->assertInstanceOf(DeliveryExecutionInterface::class, $deliveryExecution);
        
        $success = $deliveryExecution->setState('http://uri.com/fake#State');
        $this->assertTrue($success);
        
        $state = $deliveryExecution->getState();
        $this->assertEquals('http://uri.com/fake#State', $state->getUri());
        
        $success = $deliveryExecution->setState('fakeState');
        $this->assertTrue($success);
        
        $state = $deliveryExecution->getState();
        $this->assertEquals('fakeState', $state->getUri());
        
        $success = $deliveryExecution->setState('fakeState');
        $this->assertFalse($success);
    }
    
    public function testFailedStartTime()
    {
        $execution = new KVDeliveryExecution($this->getKvService(), 'http://uri.com/fake#Execution');
        $this->setExpectedException(\common_exception_NotFound::class);
        $execution->getStartTime();
        
    }

    public function testGetDeliveryExecutionsByStatus()
    {
        $userId = 'fakeUser';
        $service = $this->getKvService();
        $assembly = new \core_kernel_classes_Resource('fake');
        $de = $service->spawnDeliveryExecution('DE label', $assembly, $userId, 'http://uri.com/fake#StartState');
        $kvde = $de->getImplementation();
        $service->updateDeliveryExecutionStatus($kvde, null, 'http://uri.com/fake#FinishState');
        $persistence = $service->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID)->getPersistenceById('dummy');

        $deInStartState = json_decode($persistence->get('kve_ue_fakeUserhttp://uri.com/fake#StartState'), true);
        $deInFinishState = json_decode($persistence->get('kve_ue_fakeUserhttp://uri.com/fake#FinishState'), true);
        $this->assertEquals(1, count($deInStartState));
        $this->assertEquals(1, count($deInFinishState));

        $this->assertEquals(1, count($service->getDeliveryExecutionsByStatus($userId, 'http://uri.com/fake#StartState')));
        $this->assertEquals(0, count($service->getDeliveryExecutionsByStatus($userId, 'http://uri.com/fake#FinishState')));

        $kvde->setState('http://uri.com/fake#FinishState');

        $service->updateDeliveryExecutionStatus($kvde, null, 'http://uri.com/fake#FinishState');
        $this->assertEquals(0, count($service->getDeliveryExecutionsByStatus($userId, 'http://uri.com/fake#StartState')));
        $this->assertEquals(1, count($service->getDeliveryExecutionsByStatus($userId, 'http://uri.com/fake#FinishState')));
    }

    protected function getKvService()
    {
        $pmMock = $this->getKvMock('dummy');
        $sm = $this->getServiceManagerProphecy([
            \common_persistence_Manager::SERVICE_ID => $pmMock
        ]);
        $service = new \taoDelivery_models_classes_execution_KeyValueService([
            \taoDelivery_models_classes_execution_KeyValueService::OPTION_PERSISTENCE => 'dummy'
        ]);
        $service->setServiceLocator($sm);
        return $service;
    }
}
