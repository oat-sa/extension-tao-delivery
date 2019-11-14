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

namespace oat\taoDelivery\model\Capacity;


use oat\generis\persistence\PersistenceManager;
use oat\oatbox\event\EventManager;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\metrics\MetricsService;
use oat\taoDelivery\model\event\SystemCapacityUpdatedEvent;
use oat\taoDelivery\model\execution\Counter\DeliveryExecutionCounterInterface;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\Metrics\AwsLoadMetric;

class AwsSystemCapacityService extends ConfigurableService implements CapacityInterface
{
    const METRIC = AwsLoadMetric::class;

    const OPTION_AWS_PROBE_LIMIT = 'aws_probe';
    const OPTION_TAO_CAPACITY_LIMIT = 'tao_capacity';
    const OPTION_PERSISTENCE = 'persistence';
    const OPTION_TTL = 'ttl';

    const FALLBACK_AWS_LIMIT = 75;
    const FALLBACK_TAO_LIMIT = 100;
    const FALLBACK_TTL = 300;
    const FALLBACK_PERSISTENCE = 'metricsCache';

    const CAPACITY_CACHE_KEY = 'AwsSystemCapacityService_capacity';
    const ACTIVE_EXECUTIONS_CACHE_KEY = 'AwsSystemCapacityService_active_executions';

    /**
     * Returns the available capacity of the system
     * Will return -1 if unlimited
     *
     * @return int
     * @throws \common_Exception
     */
    public function getCapacity()
    {
        $persistence = $this->getPersistence();
        /** @var DeliveryExecutionCounterInterface $deliveryExecutionService */
        $deliveryExecutionService = $this->getServiceLocator()->get(DeliveryExecutionCounterInterface::SERVICE_ID);
        $currentActiveTestTakers = $deliveryExecutionService->count(DeliveryExecution::STATE_ACTIVE);
        $previousActiveTestTakers = (int) $persistence->get(self::ACTIVE_EXECUTIONS_CACHE_KEY);

        $cachedCapacity = $capacity = $persistence->get(self::CAPACITY_CACHE_KEY);

        if (!$cachedCapacity) {
            $awsLimit = $this->getOption(self::OPTION_AWS_PROBE_LIMIT) ?? self::FALLBACK_AWS_LIMIT;
            $taoLimit = $this->getOption(self::OPTION_TAO_CAPACITY_LIMIT) ?? self::FALLBACK_TAO_LIMIT;
            $awsMetricService = $this->getServiceLocator()->get(MetricsService::class)->getOneMetric(self::METRIC);
            $currentAwsLoad = $awsMetricService->collect();
            $capacity = (1 - $currentAwsLoad / $awsLimit) * $taoLimit;
            $persistence->set(self::CAPACITY_CACHE_KEY, $capacity, $this->getOption(self::OPTION_TTL) ?? self::FALLBACK_TTL);
            $this->getEventManager()->trigger(new SystemCapacityUpdatedEvent($cachedCapacity, $capacity));
            $persistence->set(self::ACTIVE_EXECUTIONS_CACHE_KEY, $currentActiveTestTakers);
            $previousActiveTestTakers = $currentActiveTestTakers;
        }

        $activeTestTakersDiff = $previousActiveTestTakers - $currentActiveTestTakers;
        $capacity += $activeTestTakersDiff;
        if ($capacity < 0) {
            return 0;
        }

        return $capacity;
    }

    /**
     * {@inheritdoc}
     */
    public function consume()
    {
        return $this->getCapacity() > 0;
    }

    /**
     * @return \common_persistence_KeyValuePersistence
     */
    private function getPersistence()
    {
        $persistenceId = $this->getOption(self::OPTION_PERSISTENCE) ?? self::FALLBACK_PERSISTENCE;

        return $this->getServiceLocator()->get(PersistenceManager::SERVICE_ID)->getPersistenceById($persistenceId);
    }

    /**
     * @return EventManager
     */
    private function getEventManager()
    {
        return $this->getServiceLocator()->get(EventManager::SERVICE_ID);
    }
}