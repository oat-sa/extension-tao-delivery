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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoDelivery\model\execution\Counter;

use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;

/**
 * Class DeliveryExecutionCounterService
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class DeliveryExecutionCounterService extends ConfigurableService implements DeliveryExecutionCounterInterface
{

    const OPTION_PERSISTENCE = 'persistence';
    //concatenation in constants allowed since php 5.6
    const KEY_PREFIX = self::class . '_';

    /**
     * Get number of delivery executions of given status.
     * @param $statusUri
     * @return integer
     */
    public function count($statusUri)
    {
        $persistence = $this->getPersistence();
        $key = $this->getStatusKey($statusUri);
        return intval($persistence->get($key));
    }

    /**
     * @param DeliveryExecutionState $event
     */
    public function executionStateChanged(DeliveryExecutionState $event)
    {
        $fromStatusKey = $this->getStatusKey($event->getPreviousState());
        $toStatusKey = $this->getStatusKey($event->getState());
        $persistence = $this->getPersistence();

        if ($persistence->exists($fromStatusKey) && $persistence->get($fromStatusKey) > 0) {
            $persistence->decr($fromStatusKey);
        }

        if (!$persistence->exists($toStatusKey)) {
            $persistence->set($toStatusKey, 1);
        } else {
            $persistence->incr($toStatusKey);
        }
    }

    /**
     * @param DeliveryExecutionCreated $event
     * @throws \common_exception_NotFound
     */
    public function executionCreated(DeliveryExecutionCreated $event)
    {
        $persistence = $this->getPersistence();
        $state = $event->getDeliveryExecution()->getState()->getUri();
        $toStatusKey = $this->getStatusKey($state);
        if (!$persistence->exists($toStatusKey)) {
            $persistence->set($toStatusKey, 1);
        } else {
            $persistence->incr($toStatusKey);
        }
    }

    /**
     * @return \common_persistence_KvDriver
     */
    protected function getPersistence()
    {
        return $this->getServiceLocator()
            ->get(\common_persistence_Manager::class)->getPersistenceById($this->getOption(self::OPTION_PERSISTENCE));
    }

    /**
     * @param string $statusUri
     * @return string
     */
    protected function getStatusKey($statusUri)
    {
        return self::KEY_PREFIX . $statusUri;
    }

    /**
     * @param $statusUri
     */
    public function refresh($statusUri)
    {
        //this implementation do not support refreshing
    }
}
