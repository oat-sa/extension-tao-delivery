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

use InvalidArgumentException;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\event\EventManager;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\mutex\LockTrait;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\metrics\MetricsService;
use oat\taoDelivery\model\event\SystemCapacityUpdatedEvent;
use oat\taoDelivery\model\Metrics\InfrastructureLoadMetricInterface;

class InfrastructureCapacityService extends ConfigurableService implements CapacityInterface
{
    use LoggerAwareTrait;
    use LockTrait;

    const METRIC = InfrastructureLoadMetricInterface::class;

    const OPTION_INFRASTRUCTURE_LOAD_LIMIT = 'infrastructure_load_limit';
    const OPTION_TAO_CAPACITY_LIMIT = 'tao_capacity';
    const OPTION_PERSISTENCE = 'persistence';
    const OPTION_TTL = 'ttl';

    const DEFAULT_INFRASTRUCTURE_LOAD_LIMIT = 75;
    const DEFAULT_TAO_CAPACITY_LIMIT = 100;
    const DEFAULT_TTL = 300;
    const DEFAULT_LOCK_TTL = 30;

    const CAPACITY_TO_PROVIDE_CACHE_KEY = 'infrastructure_capacity_to_provide';
    const CAPACITY_TO_CONSUME_CACHE_KEY = 'infrastructure_capacity_to_consume';

    /**
     * Returns the available capacity of the system
     * Will return -1 if unlimited
     *
     * @return int
     * @throws \common_Exception
     */
    public function getCapacity()
    {
        $cachedCapacity = $capacity = $this->getPersistence()->get(self::CAPACITY_TO_PROVIDE_CACHE_KEY);
        if ($cachedCapacity === false || $cachedCapacity === null) {
            $capacity = $this->recalculateCapacity(self::CAPACITY_TO_PROVIDE_CACHE_KEY);
        }
        if ($capacity <= 0) {
            return 0;
        }
        $this->getPersistence()->decr(self::CAPACITY_TO_PROVIDE_CACHE_KEY);

        return $capacity;
    }

    /**
     * {@inheritdoc}
     */
    public function consume()
    {
        $lock = $this->createLock(__CLASS__ . __METHOD__, $this->getLockTtl());
        $lock->acquire(true);

        try {
            $cachedCapacity = $capacity = $this->getPersistence()->get(self::CAPACITY_TO_CONSUME_CACHE_KEY);
            if ($cachedCapacity === false || $cachedCapacity === null) {
                $capacity = $this->recalculateCapacity(self::CAPACITY_TO_CONSUME_CACHE_KEY);
            }
            if ($capacity <= 0) {
                return false;
            }

            $this->getPersistence()->decr(self::CAPACITY_TO_CONSUME_CACHE_KEY);
            return true;
        } finally {
            $lock->release();
        }
    }

    /**
     * @param  string $keyToCheck
     * @return float
     * @throws \common_Exception
     */
    private function recalculateCapacity($keyToCheck)
    {
        $lock = $this->createLock(__CLASS__ . __METHOD__, $this->getLockTtl());
        $lock->acquire(true);

        try {
            $persistence = $this->getPersistence();
            $cachedValue = null;
            if ($persistence->exists($keyToCheck) && ($cachedValue = $persistence->get($keyToCheck)) !== null) {
                return $cachedValue;
            }

            $infrastructureLoadLimit = $this->getInfrastructureLoadLimit();
            $taoLimit = $this->getTaoCapacityLimit();
            $currentInfrastructureLoad = $this->collectInfrastructureLoad();

            $capacity = floor((1 - $currentInfrastructureLoad / $infrastructureLoadLimit) * $taoLimit);
            $persistence->set(self::CAPACITY_TO_PROVIDE_CACHE_KEY, $capacity, $this->getCapacityCacheTtl());
            $persistence->set(self::CAPACITY_TO_CONSUME_CACHE_KEY, $capacity, $this->getCapacityCacheTtl());

            $this->logCapacityCalculationDetails(
                $capacity,
                $currentInfrastructureLoad,
                $infrastructureLoadLimit,
                $taoLimit
            );
            $this->getEventManager()->trigger(new SystemCapacityUpdatedEvent(null, $capacity));

            return $capacity;
        } finally {
            $lock->release();
        }
    }

    /**
     * @return int
     */
    private function getLockTtl()
    {
        $capacityTtl = $this->getCapacityCacheTtl();

        return min($capacityTtl, self::DEFAULT_LOCK_TTL);
    }

    /**
     * @return \common_persistence_KeyValuePersistence
     */
    private function getPersistence()
    {
        if (!$this->hasOption(self::OPTION_PERSISTENCE)) {
            throw new InvalidArgumentException('Persistence for ' . self::SERVICE_ID . ' is not configured');
        }

        $persistenceId = $this->getOption(self::OPTION_PERSISTENCE);

        return $this->getServiceLocator()->get(PersistenceManager::SERVICE_ID)->getPersistenceById($persistenceId);
    }

    /**
     * @return EventManager
     */
    private function getEventManager()
    {
        return $this->getServiceLocator()->get(EventManager::SERVICE_ID);
    }

    private function logCapacityCalculationDetails(
        $capacity,
        $currentInfrastructureLoad,
        $infrastructureLimit,
        $taoLimit
    ) {
        $this->getLogger()->info(
            sprintf(
                'Recalculated system capacity: %s, current infrastructure load: %s%%, configured infrastructure limit: %s%%, configured TAO limit: %s',
                $capacity,
                $currentInfrastructureLoad,
                $infrastructureLimit,
                $taoLimit
            )
        );
    }

    /**
     * @return int|mixed
     */
    private function getCapacityCacheTtl()
    {
        return $this->getOption(self::OPTION_TTL) ?? self::DEFAULT_TTL;
    }

    /**
     * @return int|mixed
     */
    private function getInfrastructureLoadLimit()
    {
        return $this->getOption(self::OPTION_INFRASTRUCTURE_LOAD_LIMIT) ?? self::DEFAULT_INFRASTRUCTURE_LOAD_LIMIT;
    }

    /**
     * @return int|mixed
     */
    private function getTaoCapacityLimit()
    {
        return $this->getOption(self::OPTION_TAO_CAPACITY_LIMIT) ?? self::DEFAULT_TAO_CAPACITY_LIMIT;
    }

    /**
     * @return mixed
     */
    private function collectInfrastructureLoad()
    {
        $infrastructureMetricService = $this->getServiceLocator()->get(MetricsService::class)->getOneMetric(
            self::METRIC
        );

        return $infrastructureMetricService->collect();
    }
}
