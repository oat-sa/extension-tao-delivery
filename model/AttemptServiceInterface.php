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

namespace oat\taoDelivery\model;

use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * Service to count the attempts to pass the test.
 *
 * @access public
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 * @package taoDelivery
 */
interface AttemptServiceInterface
{
    const SERVICE_ID = 'taoDelivery/AttemptService';

    /**
     * Get array of delivery finished delivery executions which should be considered as an attempt.
     *
     * @param string $deliveryId delivery identifier
     * @param User $user
     * @return DeliveryExecutionInterface[]
     */
    public function getAttempts($deliveryId, User $user);

    /**
     * Set array of states to be excluded (execution in this state will not be considered as an attempt)
     * @param array $deliveryExecutionsStates
     */
    public function setStatesToExclude(array $deliveryExecutionsStates);

    /**
     * Get array of states to be excluded
     */
    public function getStatesToExclude();
}
