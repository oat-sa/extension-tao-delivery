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

namespace oat\taoDelivery\test\unit\model\execution\implementation;

use common_persistence_KeyValuePersistence;
use common_persistence_Manager;
use oat\generis\test\MockObject;
use oat\generis\test\PersistenceManagerMockTrait;
use oat\generis\test\ServiceManagerMockTrait;
use oat\tao\model\browser\Entity\LockDownBrowserConfiguration;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\implementation\KeyValueService;
use oat\taoDelivery\model\execution\KVDeliveryExecution;
use PHPUnit\Framework\TestCase;

class KeyValueServiceTest extends TestCase
{
    use PersistenceManagerMockTrait;
    use ServiceManagerMockTrait;

    /** @var KeyValueService|MockObject */
    private $subject;

    public function setUp(): void
    {
        $persistence = $this->createMock(common_persistence_KeyValuePersistence::class);
        $persistence->expects($this->any())->method('get')->willReturn(json_encode(['test']));

        $this->subject = $this->getMockBuilder(KeyValueService::class)->onlyMethods(
            ['getOption', 'updateDeliveryExecutionStatus', 'getPersistence']
        )->getMock();
        $this->subject->expects($this->any())->method('getOption')->willReturn('test');
        $this->subject->expects($this->any())->method('getPersistence')->willReturn($persistence);

        $persistenceManager = $this->getPersistenceManagerMock('test');

        $this->subject->setServiceLocator(
            $this->getServiceManagerMock([
                common_persistence_Manager::SERVICE_ID => $persistenceManager,
            ])
        );
    }

    public function testSpawnDeliveryExecution()
    {
        $this->subject->expects($this->once())->method('updateDeliveryExecutionStatus')->willReturn('ok');
        $this->assertInstanceOf(
            DeliveryExecution::class,
            $this->subject->spawnDeliveryExecution("test", "test", "test", "test", "test")
        );
    }

    public function testSpawnDeliveryExecutionOmitsLockDownBrowserWhenNotProvided(): void
    {
        $this->subject->expects($this->once())->method('updateDeliveryExecutionStatus')->willReturn('ok');

        $execution = $this->subject->spawnDeliveryExecution('label', 'delivery', 'user', 'status', 'exec-id');
        $implementation = $execution->getImplementation();

        $this->assertInstanceOf(KVDeliveryExecution::class, $implementation);
        $this->assertNull($implementation->getLockDownBrowser());
    }

    public function testSpawnDeliveryExecutionOmitsLockDownBrowserWhenEmptyString(): void
    {
        $this->subject->expects($this->once())->method('updateDeliveryExecutionStatus')->willReturn('ok');

        $execution = $this->subject->spawnDeliveryExecution(
            'label',
            'delivery',
            'user',
            'status',
            'exec-id',
            ''
        );

        $this->assertNull($execution->getImplementation()->getLockDownBrowser());
    }

    public function testSpawnDeliveryExecutionStoresLockDownBrowserWhenProvided(): void
    {
        $this->subject->expects($this->once())->method('updateDeliveryExecutionStatus')->willReturn('ok');

        $execution = $this->subject->spawnDeliveryExecution(
            'label',
            'delivery',
            'user',
            'status',
            'exec-id',
            LockDownBrowserConfiguration::PROVIDER_KIOSKED
        );

        $this->assertSame(LockDownBrowserConfiguration::PROVIDER_KIOSKED, $execution->getLockDownBrowser());
        $this->assertSame(
            LockDownBrowserConfiguration::PROVIDER_KIOSKED,
            $execution->getImplementation()->getLockDownBrowser()
        );
    }
}
