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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\taoDelivery\model\execution;

use core_kernel_classes_Resource;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDelete;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
interface Service extends DeliveryExecutionDelete
{
    /**
     * Returns the executions the user has of a specified assembly
     *
     * @param core_kernel_classes_Resource $assembly
     * @param string $userUri
     * @return array
     * @internal param core_kernel_classes_Resource $compiled
     */
    public function getUserExecutions(core_kernel_classes_Resource $assembly, $userUri);
    
    /**
     * Returns all Delivery Executions of a User with a specific status
     *
     * @param string $userUri
     * @param string $status
     * @return array
     */
    public function getDeliveryExecutionsByStatus($userUri, $status);
    
    /**
     * Generate a new delivery execution
     *
     * @param core_kernel_classes_Resource $assembly
     * @param User $user
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $user);
    
    /**
     * Returns the delivery execution instance associated to the implementation
     * 
     * @param string $identifier
     * @return DeliveryExecution
     */
    public function getDeliveryExecution($identifier);    
}
