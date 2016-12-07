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

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
interface taoDelivery_models_classes_execution_DeliveryExecution
{

    /**
     * Returns the identifier of the delivery execution
     *
     * @throws \common_exception_NotFound
     * @return string
     */
    public function getIdentifier();
    
    /**
     * Returns a human readable test representation of the delivery execution
     * Should respect the current user's language
     *
     * @throws \common_exception_NotFound
     * @return string
     */
    public function getLabel();
    
    /**
     * Returns when the delivery execution was started
     *
     * @throws \common_exception_NotFound
     * @return string
     */
    public function getStartTime();
    
    /**
     * Returns when the delivery execution was finished
     * or null if not yet finished
     *
     * @throws \common_exception_NotFound
     * * @return string | null if the execution is not yet finished
     */
    public function getFinishTime();

    /**
     * Returns the delivery execution state as resource
     *
     * @throws \common_exception_NotFound
     * @return core_kernel_classes_Resource 
     */
    public function getState();
    
    /**
     * 
     * @param string $state
     * @return boolean success
     */
    public function setState($state);
    
    /**
     * Returns the delivery execution delivery as resource
     *
     * @throws \common_exception_NotFound
     * @return core_kernel_classes_Resource
     */
    public function getDelivery();
    
    /**
     * Returns the delivery executions user identifier
     *
     * @throws \common_exception_NotFound
     * @return string
    */
    public function getUserIdentifier();
}
