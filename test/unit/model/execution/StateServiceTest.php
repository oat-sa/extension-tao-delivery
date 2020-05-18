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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 * @author Sergei Mikhailov <sergei.mikhailov@taotesting.com>
 */

namespace oat\taoDelivery\test\unit\model\execution;

use core_kernel_classes_Resource as CoreResource;
use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\oatbox\event\Event;
use oat\oatbox\event\EventManager;
use oat\oatbox\log\LoggerService;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\model\execution\implementation\KeyValueService;
use oat\taoDelivery\model\execution\StateService;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionReactivated;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;
use Psr\Log\NullLogger;

class StateServiceTest extends TestCase
{
    private const TEST_DELIVERY_ID = 'delivery_id_1';

    /** @var User|MockObject */
    private $user;

    /** @var KeyValueService|MockObject */
    private $storage;

    /** @var EventManager|MockObject */
    private $eventManager;

    /**
     * @before
     */
    public function initializeDependencies(): void
    {
        $this->user         = $this->createMock(User::class);
        $this->storage      = $this->createMock(KeyValueService::class);
        $this->eventManager = $this->createMock(EventManager::class);

        $this->user
            ->method('getIdentifier')
            ->willReturn('user_id_1');
    }

    public function testDeliveryExecutionCreation(): void
    {
        $sut = $this->createSut();

        $label             = 'test';
        $deliveryExecution = $this->createMock(DeliveryExecutionInterface::class);

        $this->storage
            ->expects(static::once())
            ->method('spawnDeliveryExecution')
            ->with(
                $label,
                self::TEST_DELIVERY_ID,
                $this->user->getIdentifier(),
                DeliveryExecutionInterface::STATE_ACTIVE
            )
            ->willReturn($deliveryExecution);

        $this->expectEvents(new DeliveryExecutionCreated($deliveryExecution, $this->user));

        $this->assertSame(
            $deliveryExecution,
            $sut->createDeliveryExecution(self::TEST_DELIVERY_ID, $this->user, $label)
        );
    }

    public function testDeliveryExecutionWithCustomInitialStatusCreation(): void
    {
        $initialStatus = DeliveryExecutionInterface::STATE_TERMINATED;

        $sut = $this->createOverriddenInitialStatusImplementation($initialStatus);

        $label             = 'test';
        $deliveryExecution = $this->createMock(DeliveryExecutionInterface::class);

        $this->storage
            ->expects(static::once())
            ->method('spawnDeliveryExecution')
            ->with(
                $label,
                self::TEST_DELIVERY_ID,
                $this->user->getIdentifier(),
                $initialStatus
            )
            ->willReturn($deliveryExecution);

        $this->expectEvents(new DeliveryExecutionCreated($deliveryExecution, $this->user));

        $this->assertSame(
            $deliveryExecution,
            $sut->createDeliveryExecution(self::TEST_DELIVERY_ID, $this->user, $label)
        );
    }

    public function testReactivateExecution(): void
    {
        $state       = DeliveryExecutionInterface::STATE_TERMINATED;
        $futureState = DeliveryExecutionInterface::STATE_PAUSED;

        $deliveryExecution = $this->createDeliveryExecution($state, $futureState);
        $reason            = 'test_reason_1';

        $this->expectEvents(
            new DeliveryExecutionState($deliveryExecution, $futureState, $state),
            new DeliveryExecutionReactivated($deliveryExecution, $this->user, $reason)
        );

        $this->assertTrue($this->createSut()->reactivateExecution($deliveryExecution, $reason));
    }

    public function testFailToReactivateExecution(): void
    {
        $state = DeliveryExecutionInterface::STATE_PAUSED;

        $deliveryExecution = $this->createDeliveryExecution($state);
        $reason            = 'test_reason_1';

        $this->expectEvents();

        $this->assertFalse($this->createSut()->reactivateExecution($deliveryExecution, $reason));
    }

    public function testRunTerminated(): void
    {
        $state       = DeliveryExecutionInterface::STATE_TERMINATED;
        $futureState = DeliveryExecutionInterface::STATE_ACTIVE;

        $deliveryExecution = $this->createDeliveryExecution($state, $futureState);

        $this->expectEvents(
            new DeliveryExecutionState($deliveryExecution, $futureState, $state),
            new DeliveryExecutionReactivated($deliveryExecution, $this->user)
        );

        $this->assertTrue($this->createSut()->run($deliveryExecution));
    }

    public function testRunPaused(): void
    {
        $state       = DeliveryExecutionInterface::STATE_PAUSED;
        $futureState = DeliveryExecutionInterface::STATE_ACTIVE;

        $deliveryExecution = $this->createDeliveryExecution($state, $futureState);

        $this->expectEvents(
            new DeliveryExecutionState($deliveryExecution, $futureState, $state)
        );

        $this->assertTrue($this->createSut()->run($deliveryExecution));
    }

    public function testPauseRunning(): void
    {
        $state       = DeliveryExecutionInterface::STATE_ACTIVE;
        $futureState = DeliveryExecutionInterface::STATE_PAUSED;

        $deliveryExecution = $this->createDeliveryExecution($state, $futureState);

        $this->expectEvents(
            new DeliveryExecutionState($deliveryExecution, $futureState, $state)
        );

        $this->assertTrue($this->createSut()->pause($deliveryExecution));
    }

    public function testPauseTerminated(): void
    {
        $state       = DeliveryExecutionInterface::STATE_TERMINATED;
        $futureState = DeliveryExecutionInterface::STATE_PAUSED;

        $deliveryExecution = $this->createDeliveryExecution($state, $futureState);

        $this->expectEvents(
            new DeliveryExecutionState($deliveryExecution, $futureState, $state),
            new DeliveryExecutionReactivated($deliveryExecution, $this->user)
        );

        $this->assertTrue($this->createSut()->pause($deliveryExecution));
    }

    public function testFinish(): void
    {
        $state       = DeliveryExecutionInterface::STATE_ACTIVE;
        $futureState = DeliveryExecutionInterface::STATE_FINISHED;

        $deliveryExecution = $this->createDeliveryExecution($state, $futureState);

        $this->expectEvents(
            new DeliveryExecutionState($deliveryExecution, $futureState, $state)
        );

        $this->assertTrue($this->createSut()->finish($deliveryExecution));
    }

    public function testTerminate(): void
    {
        $state       = DeliveryExecutionInterface::STATE_ACTIVE;
        $futureState = DeliveryExecutionInterface::STATE_TERMINATED;

        $deliveryExecution = $this->createDeliveryExecution($state, $futureState);

        $this->expectEvents(
            new DeliveryExecutionState($deliveryExecution, $futureState, $state)
        );

        $this->assertTrue($this->createSut()->terminate($deliveryExecution));
    }

    private function createOverriddenInitialStatusImplementation(string $initialStatus): StateService
    {
        $stateService = $this->createSut('getInitialStatus');

        $stateService
            ->method('getInitialStatus')
            ->with(self::TEST_DELIVERY_ID, $this->user)
            ->willReturn($initialStatus);

        return $stateService;
    }

    /**
     * @param string ...$overriddenMethods
     *
     * @return StateService|MockObject
     */
    private function createSut(string ...$overriddenMethods): StateService
    {
        $overriddenMethods[] = 'getUser';

        $sut = $this->createPartialMock(StateService::class, $overriddenMethods);

        $sut
            ->method('getUser')
            ->willReturn($this->user);

        $sut->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    StateService::STORAGE_SERVICE_ID => $this->storage,
                    EventManager::SERVICE_ID         => $this->eventManager,
                    LoggerService::SERVICE_ID        => new NullLogger(),
                ]
            )
        );

        return $sut;
    }

    private function createDeliveryExecution(string $state, string $futureState = null): DeliveryExecution
    {
        $deliveryExecution = $this->createMock(DeliveryExecutionInterface::class);

        $stateResource = $this->createStateResource($state);

        $deliveryExecution
            ->method('getState')
            ->willReturn($stateResource);

        $deliveryExecution
            ->expects($futureState ? static::once() : static::never())
            ->method('setState')
            ->with($futureState)
            ->willReturn(true);

        return new DeliveryExecution($deliveryExecution);
    }

    private function createStateResource(string $state): CoreResource
    {
        $resource = $this->createMock(CoreResource::class);

        $resource
            ->method('getUri')
            ->willReturn($state);

        return $resource;
    }

    private function expectEvents(Event ...$events): void
    {
        $this->eventManager
            ->expects(static::exactly(count($events)))
            ->method('trigger')
            ->withConsecutive(
                ...array_map(
                       static function (Event $event): array {
                           return [$event];
                       },
                       $events
                   )
            );
    }
}
