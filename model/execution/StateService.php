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

use oat\oatbox\user\User;
/**
 * Class StateService
 * @package oat\taoDelivery
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class StateService extends AbstractStateService
{
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\execution\AbstractStateService::getInitialStatus()
     */
    public function getInitialStatus($deliveryId, User $user)
    {
       return DeliveryExecution::STATE_ACTIVE;
    }

    /**
     * @param DeliveryExecution $deliveryExecution
     * @return bool
     * @throws \common_exception_NotFound
     */
    public function finish(DeliveryExecution $deliveryExecution)
    {
        return $this->setState($deliveryExecution, DeliveryExecution::STATE_FINISHED);
    }

    /**
     * @param DeliveryExecution $deliveryExecution
     * @return bool
     */
    public function run(DeliveryExecution $deliveryExecution)
    {
        return $this->setState($deliveryExecution, DeliveryExecution::STATE_ACTIVE);
    }

    /**
     * @param DeliveryExecution $deliveryExecution
     * @return bool
     * @throws \common_exception_NotFound
     */
    public function pause(DeliveryExecution $deliveryExecution)
    {
        return $this->setState($deliveryExecution, DeliveryExecution::STATE_PAUSED);
    }

    /**
     * Terminate a delivery execution with an optional reason
     * @param DeliveryExecution $deliveryExecution
     * @return boolean
     */
     public function terminate(DeliveryExecution $deliveryExecution)
    {
        return $this->setState($deliveryExecution, DeliveryExecution::STATE_TERMINATED);
    }

    /**
     * Legacy function to ensure all calls to setState use
     * the correct transition instead
     *
     * @param DeliveryExecution $deliveryExecution
     * @param string $state
     * @return bool
     * @throws \common_exception_NotFound
     */
    public function legacyTransition(DeliveryExecution $deliveryExecution, $state)
    {
        switch ($state) {
            case DeliveryExecution::STATE_FINISHED:
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

    /**
     * @return array
     */
    public function getDeliveriesStates()
     {
         return [
             DeliveryExecution::STATE_FINISHED,
             DeliveryExecution::STATE_ACTIVE,
             DeliveryExecution::STATE_PAUSED
         ];
     }
}
