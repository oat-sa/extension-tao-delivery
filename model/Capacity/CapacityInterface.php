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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDelivery\model\Capacity;

/**
 * Service that returns the number of users the system cann support
 * in addition to the users already using the system
 */
interface CapacityInterface
{

    const SERVICE_ID = 'taoDelivery/CapacityService';

    /**
     * Returns the available capacity of the system
     * Will return -1 if unlimited
     *
     * @return int
     */
    public function getCapacity();

    /**
     * transaction safe function to consume capacity
     * returns true on success, false on failure
     *
     * @return boolean
     */
    public function consume();
}
