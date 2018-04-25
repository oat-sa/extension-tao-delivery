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

        if ($this->count($event->getPreviousState()) > 0) {
            $persistence->decr($fromStatusKey);
        }

        if ($persistence->get($toStatusKey) === false) {
            $persistence->set($toStatusKey, 0);
        }
        $persistence->incr($toStatusKey);
    }

    /**
     * @return \common_persistence_KvDriver
     */
    private function getPersistence()
    {
        return $this->getServiceLocator()
            ->get(\common_persistence_Manager::class)->getPersistenceById($this->getOption(self::OPTION_PERSISTENCE));
    }

    /**
     * @param string $statusUri
     * @return string
     */
    private function getStatusKey($statusUri)
    {
        return self::KEY_PREFIX . $statusUri;
    }
}
