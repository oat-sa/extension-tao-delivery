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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDelivery\test\integration\model\execution\Counter;

use oat\oatbox\service\ServiceManager;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;
use oat\taoDelivery\model\execution\Counter\DeliveryExecutionCounterService;

/**
 * Class DeliveryExecutionCounterServiceTest
 * @package oat\taoDelivery\test\Counter\DeliveryExecutionCounterServiceTest
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class DeliveryExecutionCounterServiceTest extends TaoPhpUnitTestRunner
{

    public function testCount()
    {
        $serviceManager = $this->buildServiceManager();
        $service = $serviceManager->get(DeliveryExecutionCounterService::SERVICE_ID);

        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_ACTIVE));
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_PAUSED));
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_FINISHED));

        // activate
        $event = new DeliveryExecutionState(
            $this->mockDeliveryExecution(),
            DeliveryExecutionInterface::STATE_ACTIVE,
            DeliveryExecutionInterface::STATE_PAUSED
        );
        $service->executionStateChanged($event);
        $this->assertEquals(1, $service->count(DeliveryExecutionInterface::STATE_ACTIVE));
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_PAUSED));
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_FINISHED));

        // activate
        $event = new DeliveryExecutionState(
            $this->mockDeliveryExecution(),
            DeliveryExecutionInterface::STATE_ACTIVE,
            DeliveryExecutionInterface::STATE_PAUSED
        );
        $service->executionStateChanged($event);
        $this->assertEquals(2, $service->count(DeliveryExecutionInterface::STATE_ACTIVE));
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_PAUSED));
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_FINISHED));

        // finish active
        $event = new DeliveryExecutionState(
            $this->mockDeliveryExecution(),
            DeliveryExecutionInterface::STATE_FINISHED,
            DeliveryExecutionInterface::STATE_ACTIVE
        );
        $service->executionStateChanged($event);
        $this->assertEquals(1, $service->count(DeliveryExecutionInterface::STATE_ACTIVE));
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_PAUSED));
        $this->assertEquals(1, $service->count(DeliveryExecutionInterface::STATE_FINISHED));

        // pause active
        $event = new DeliveryExecutionState(
            $this->mockDeliveryExecution(),
            DeliveryExecutionInterface::STATE_PAUSED,
            DeliveryExecutionInterface::STATE_ACTIVE
        );
        $service->executionStateChanged($event);
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_ACTIVE));
        $this->assertEquals(1, $service->count(DeliveryExecutionInterface::STATE_PAUSED));
        $this->assertEquals(1, $service->count(DeliveryExecutionInterface::STATE_FINISHED));

        // pause active
        $event = new DeliveryExecutionState(
            $this->mockDeliveryExecution(),
            DeliveryExecutionInterface::STATE_PAUSED,
            DeliveryExecutionInterface::STATE_ACTIVE
        );
        $service->executionStateChanged($event);
        $this->assertEquals(0, $service->count(DeliveryExecutionInterface::STATE_ACTIVE));
        $this->assertEquals(2, $service->count(DeliveryExecutionInterface::STATE_PAUSED));
        $this->assertEquals(1, $service->count(DeliveryExecutionInterface::STATE_FINISHED));
    }

    /**
     * @return ServiceManager
     * @throws \common_Exception
     */
    private function buildServiceManager()
    {
        $config = new \common_persistence_KeyValuePersistence([], new \common_persistence_InMemoryKvDriver());
        $serviceManager = new ServiceManager($config);
        $serviceManager->register(DeliveryExecutionCounterService::SERVICE_ID, new DeliveryExecutionCounterService(
            [DeliveryExecutionCounterService::OPTION_PERSISTENCE => 'kv_persistence']
        ));
        $serviceManager->register(\common_persistence_Manager::SERVICE_ID, new \common_persistence_Manager([
            'persistences' => [
                'kv_persistence' => [
                    'driver' => 'no_storage'
                ]
            ]
        ]));

        return $serviceManager ;
    }

    /**
     * @return DeliveryExecutionInterface
     */
    private function mockDeliveryExecution()
    {
        return $this->getMockBuilder(DeliveryExecutionInterface::class)
            ->getMock();
    }
}
