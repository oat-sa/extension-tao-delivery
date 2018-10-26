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
 */

namespace oat\taoDelivery\model;

use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecution;

/**
 * Helper to render the delivery form on the group page
 */
interface DeliveryRendererHelperServiceInterface
{
    const SERVICE_ID = 'taoDelivery/DeliveryRendererHelperService';

    const ID = 'id';
    const LABEL = 'label';
    const AUTHORIZED = 'TAO_DELIVERY_TAKABLE';
    const DESCRIPTION = 'description';
    const LAUNCH_URL = 'launchUrl';

    public function buildFromAssembly($assignment, User $user);
    public function buildFromDeliveryExecution(DeliveryExecution $deliveryExecution);
}