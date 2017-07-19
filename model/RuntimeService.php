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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\model;

use oat\taoDelivery\model\container\DeliveryContainer;
/**
 * Service to manage the assignment of users to deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
interface RuntimeService
{
    const SERVICE_ID = 'taoDelivery/Runtime';

    /**
     * Provides the container to run the delivery
     *
     * @param string $deliveryId
     * @return DeliveryContainer
     */
    public function getDeliveryContainer($deliveryId);

    /**
     * Gets the service call to run this assembly.
     * Currently still required as many custom extensions retireve
     * delivery data from the runtime (breaking abstraction layers)
     *
     * @param string $deliveryId
     * @return \tao_models_classes_service_ServiceCall
     * @deprecated
     */
    public function getRuntime($deliveryId);
}