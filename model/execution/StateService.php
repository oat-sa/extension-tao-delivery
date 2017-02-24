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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoDelivery\model\execution;

use oat\taoDelivery\model\execution\DeliveryExecution as DeliveryExecutionInterface;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState as DeliveryExecutionStateEvent;
use oat\oatbox\event\EventManager;

/**
 * Class StateService
 * @package oat\taoDelivery
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class StateService extends ConfigurableService implements StateServiceInterface
{
    /**
     * @param DeliveryExecutionInterface $deliveryExecution
     * @param string $state
     * @return bool
     */
    public function setState(DeliveryExecutionInterface $deliveryExecution, $state)
    {
        $prevState = $deliveryExecution->getState();
        $result = $deliveryExecution->setState($state);

        $event = new DeliveryExecutionStateEvent($deliveryExecution, $state, $prevState->getUri());
        $this->getServiceManager()->get(EventManager::SERVICE_ID)->trigger($event);
        \common_Logger::i("DeliveryExecutionState Event triggered.");

        return $result;
    }
}
