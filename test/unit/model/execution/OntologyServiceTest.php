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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDelivery\test\unit\model\execution;

use oat\generis\test\MockObject;
use oat\generis\test\PersistenceManagerMockTrait;
use oat\generis\test\ServiceManagerMockTrait;
use oat\oatbox\event\EventManager;
use PHPUnit\Framework\TestCase;
use oat\oatbox\log\LoggerService;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\OntologyService;
use common_persistence_Manager;
use core_kernel_classes_Resource;
use core_kernel_classes_Class;

class OntologyServiceTest extends TestCase
{
    use PersistenceManagerMockTrait;
    use ServiceManagerMockTrait;

    /** @var OntologyService|MockObject */
    private $subject;

    public function setUp(): void
    {
        $resourceMock = $this->createMock(core_kernel_classes_Resource::class);
        $resourceMock->expects($this->once())->method('setPropertiesValues');

        $kernelClassMock = $this->getMockBuilder(core_kernel_classes_Class::class)
            ->disableOriginalConstructor()->onlyMethods([
            'createInstanceWithProperties', 'getResource'
        ])->getMock();
        $kernelClassMock->method('getResource')->willReturn($resourceMock);

        $this->subject = $this->getMockBuilder(OntologyService::class)->onlyMethods(
            ['getClass']
        )->getMock();

        $this->subject->expects($this->once())->method('getClass')->willReturn($kernelClassMock);

        $loggerServiceMock = $this->createMock(LoggerService::class);
        $loggerServiceMock->method('setLogger')->willReturn('ok');

        $this->subject->setServiceLocator($this->getServiceManagerMock([
            common_persistence_Manager::SERVICE_ID => $this->getPersistenceManagerMock('test'),
            EventManager::SERVICE_ID => $this->createMock(EventManager::class)
        ]));
    }


    public function testSpawnDeliveryExecution()
    {
        $this->assertInstanceOf(
            DeliveryExecution::class,
            $this->subject->spawnDeliveryExecution("test", "test", "test", "test", "test")
        );
    }
}
