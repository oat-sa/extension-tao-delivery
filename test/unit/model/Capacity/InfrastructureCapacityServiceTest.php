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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoDelivery\test\unit\model\Capacity;

use common_persistence_KeyValuePersistence;
use oat\generis\persistence\PersistenceManager;
use oat\generis\test\TestCase;
use oat\oatbox\event\EventManager;
use oat\oatbox\log\LoggerService;
use oat\oatbox\mutex\LockService;
use oat\tao\model\metrics\MetricsService;
use oat\taoDelivery\model\Capacity\InfrastructureCapacityService;
use oat\taoDelivery\model\Metrics\AwsLoadMetric;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\LockInterface;

class InfrastructureCapacityServiceTest extends TestCase
{
    /**
     * @dataProvider provideConfigAndMetricDataForCheckCapacity
     */
    public function testGetCapacity_WhenConfigOptionsAndMetricDataIsProvided_ThenExpectedValuesAreReturned(
        $infrastructureLimit,
        $taoLimit,
        $cachedCapacity,
        $currentInfrastructureLoad,
        $expectedCapacity
    ) {
        $service = $this->createInfrastructureCapacityService($infrastructureLimit, $taoLimit, $cachedCapacity, $currentInfrastructureLoad);

        $this->assertEquals($expectedCapacity, $service->getCapacity());
    }

    public function testConsume_WhenCachedServerCapacityIsInsufficient_ThenFalseIsReturned()
    {
        $service = $this->createInfrastructureCapacityService(50, 100, 0, 50);
        $this->assertFalse($service->consume());
    }

    public function testConsume_WhenCachedServerCapacityIsSufficient_ThenTrueReturnedAndCapacityDecremented()
    {
        $service = $this->createInfrastructureCapacityService(50, 100, 1, 50, true);
        $this->assertTrue($service->consume());
    }

    /**
     * returns array with following structure:
     * <code>
     * array(
     *     array(
     *          (int) infrastructureLimit,
     *          (int) taoLimit,
     *          (int) cachedCapacity,
     *          (int) currentInfrastructureLoad,
     *          (int) expectedCapacity
     *     )
     * )
     * </code>
     *
     * @return array[]
     */
    public function provideConfigAndMetricDataForCheckCapacity()
    {
        return [
            // when there is no cached capacity, and server load is lower than configured threshold,
            // then calculated capacity is proportional amount from configured TAO limit
            [80, 3000, null, 40, 1500], // no cached capacity, server load limit 80%, current load 40%
            [90, 3000, null, 30, 2000], // no cached capacity, server load limit 90%, current load 30%
            // when there is no cached capacity, and server load is larger than configured threshold,
            // then calculated capacity is negative and a zero capacity is returned
            [80, 3000, null, 80, 0],
            [80, 3000, null, 81, 0],
            [80, 3000, null, 100, 0],
            // when there is a cached calculated server capacity, then it is returned
            [80, 3000, 5, 100, 5],
            [80, 3000, 50, 100, 50],
        ];
    }

    /**
     * @param $infrastructureLimit
     * @param $taoLimit
     * @param $cachedCapacity
     * @param $currentInfrastructureLoad
     * @param bool $shouldDecrementCapacity
     * @return InfrastructureCapacityService
     */
    private function createInfrastructureCapacityService(
        $infrastructureLimit,
        $taoLimit,
        $cachedCapacity,
        $currentInfrastructureLoad,
        $shouldDecrementCapacity = false
    ) {
        $serviceLocatorMock = $this->getServiceLocatorMock([
            LockService::SERVICE_ID => $this->createLockServiceMock(),
            PersistenceManager::SERVICE_ID => $this->createPersistenceManagerMock($cachedCapacity, $shouldDecrementCapacity),
            EventManager::SERVICE_ID => $this->createMock(EventManager::class),
            LoggerService::SERVICE_ID => $this->createMock(LoggerInterface::class),
            MetricsService::class => $this->createMetricsMock($currentInfrastructureLoad),
        ]);
        $service = new InfrastructureCapacityService([
            InfrastructureCapacityService::OPTION_INFRASTRUCTURE_LOAD_LIMIT => $infrastructureLimit,
            InfrastructureCapacityService::OPTION_TAO_CAPACITY_LIMIT => $taoLimit,
            InfrastructureCapacityService::OPTION_TTL => 60,
            InfrastructureCapacityService::OPTION_PERSISTENCE => 'testPersistence',
        ]);
        $service->setServiceLocator($serviceLocatorMock);

        return $service;
    }

    /**
     * @param $currentInfrastructureLoad
     * @return \oat\generis\test\MockObject
     */
    private function createMetricsMock($currentInfrastructureLoad)
    {
        $awsLoadMetricMock = $this->createMock(AwsLoadMetric::class);
        $awsLoadMetricMock->method('collect')->willReturn($currentInfrastructureLoad);
        $metricsServiceMock = $this->createMock(MetricsService::class);
        $metricsServiceMock->method('getOneMetric')->willReturn($awsLoadMetricMock);

        return $metricsServiceMock;
    }

    /**
     * @param $cachedCapacity
     * @return \oat\generis\test\MockObject
     */
    private function createPersistenceManagerMock($cachedCapacity, $shouldDecrementCapacity)
    {
        $persistenceManagerMock = $this->createMock(PersistenceManager::class);
        $persistenceMock = $this->createMock(common_persistence_KeyValuePersistence::class);
        $persistenceMock->method('get')->willReturnCallback(function ($argument) use ($cachedCapacity) {
            switch ($argument) {
                case InfrastructureCapacityService::CAPACITY_TO_PROVIDE_CACHE_KEY:
                case InfrastructureCapacityService::CAPACITY_TO_CONSUME_CACHE_KEY:
                    return $cachedCapacity;
            }
        });
        if ($shouldDecrementCapacity) {
            $persistenceMock->expects($this->once())->method('decr')->willReturn(true);
        }
        $persistenceManagerMock->method('getPersistenceById')->willReturn($persistenceMock);

        return $persistenceManagerMock;
    }

    /**
     * @return \oat\generis\test\MockObject
     */
    private function createLockServiceMock()
    {
        $lockServiceMock = $this->createMock(LockService::class);
        $lockFactoryMock = $this->createMock(Factory::class);
        $lockServiceMock->method('getLockFactory')->willReturn($lockFactoryMock);
        $lockMock = $this->createMock(LockInterface::class);
        $lockFactoryMock->method('createLock')->willReturn($lockMock);

        return $lockServiceMock;
    }
}
