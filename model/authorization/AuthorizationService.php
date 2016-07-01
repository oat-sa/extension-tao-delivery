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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\model\authorization;

use oat\taoDelivery\models\classes\execution\DeliveryExecution;


/**
 * Manage the Delivery authorization.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
interface AuthorizationService
{
    const CONFIG_ID = 'taoDelivery/authorization';

    /**
     * Returns the the authorization provider
     *
     * @param DeliveryExecution $deliveryExecution the delivery to authorize
     * @return AuthorizationProviderService
     */
    public function getAuthorizationProvider(DeliveryExecution $deliveryExecution);
}
