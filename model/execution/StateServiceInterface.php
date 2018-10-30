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
 * Interface StateServiceInterface
 *
 * Service is used to handle changing of delivery execution state.
 *
 * @package oat\taoDelivery
 */
interface StateServiceInterface
{
    const SERVICE_ID = 'taoDelivery/stateService';

    const STORAGE_SERVICE_ID = 'taoDelivery/execution_service';

    /**
     * Spawns a new delivery execution
     *
     * @param string $deliveryId
     * @param User $user
     * @param $label
     */
    public function createDeliveryExecution($deliveryId, User $user, $label);

    public function run(DeliveryExecution $deliveryExecution);

    public function pause(DeliveryExecution $deliveryExecution);

    public function finish(DeliveryExecution $deliveryExecution);

    /**
     * Terminates a delivery execution
     *
     * @param DeliveryExecution $deliveryExecution
     * @return bool
     */
    public function terminate(DeliveryExecution $deliveryExecution);

    public function getDeliveriesStates();

    /**
     * @param DeliveryExecution $deliveryExecution
     * @param null $reason
     * @return mixed
     */
    public function reactivateExecution(DeliveryExecution $deliveryExecution, $reason = null);
}
