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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoDelivery\model\authorization;

use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * Provides authorization capabilities.
 * The provider needs to be contextualized, it answer based on it's current state.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
interface AuthorizationProvider
{

    /**
     * Verify that a given delivery is allowed to be started
     * 
     * @param string $deliveryId
     * @throws \common_exception_Unauthorized
     */
    public function verifyStartAuthorization($deliveryId, User $user);
    
    /**
     * Verify that a given delivery execution is allowed to be executed
     * 
     * @param DeliveryExecutionInterface $deliveryExecution
     * @param User $user
     * @throws \common_exception_Unauthorized
     */
    public function verifyResumeAuthorization(DeliveryExecutionInterface $deliveryExecution, User $user);
}
