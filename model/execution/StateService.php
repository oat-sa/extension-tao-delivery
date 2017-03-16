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

use oat\taoDelivery\models\classes\execution\DeliveryExecution as BaseDeliveryExecution;

/**
 * Class StateService
 * @package oat\taoDelivery
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class StateService extends AbstractStateService
{

    /**
     * @param DeliveryExecution $deliveryExecution
     * @return bool
     */
    public function finish(BaseDeliveryExecution $deliveryExecution)
    {
        return $this->setState($deliveryExecution, DeliveryExecution::STATE_FINISHIED);
    }

    /**
     * @param DeliveryExecution $deliveryExecution
     * @return bool
     */
    public function run(BaseDeliveryExecution $deliveryExecution)
    {
        return $this->setState($deliveryExecution, DeliveryExecution::STATE_ACTIVE);
    }

    /**
     * @param DeliveryExecution $deliveryExecution
     * @return bool
     */
    public function pause(BaseDeliveryExecution $deliveryExecution)
    {
        return $this->setState($deliveryExecution, DeliveryExecution::STATE_PAUSED);
    }

    /**
     * Legacy function to ensure all calls to setState use
     * the correct transition instead
     *
     * @param DeliveryExecution $deliveryExecution
     * @param string $state
     * @return bool
     */
    public function legacyTransition(BaseDeliveryExecution $deliveryExecution, $state)
    {
        switch ($state) {
            case DeliveryExecution::STATE_FINISHIED:
                $result = $this->finish($deliveryExecution);
                break;
            case DeliveryExecution::STATE_ACTIVE:
                $result = $this->run($deliveryExecution);
                break;
            case DeliveryExecution::STATE_PAUSED:
                $result = $this->pause($deliveryExecution);
                break;
            default:
                $this->logWarning('Unrecognised state '.$state);
                $result = $this->setState($deliveryExecution, $state);
        }
        return $result;
    }
}
