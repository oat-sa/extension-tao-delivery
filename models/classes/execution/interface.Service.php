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
interface taoDelivery_models_classes_execution_Service
{
    /**
     * a reminder that services are singletons
     */
    public static function singleton();
    
    /**
     * Returns how many executions the user has of a specified assembly
     * 
     * @param core_kernel_classes_Resource $compiled
     * @param string $userUri
     */
    public function getUserExecutionCount(core_kernel_classes_Resource $assembly, $userUri);
    
    /**
     * Returns all activ Delivery Executions of a User
     *
     * @param unknown $userUri
     * @return Ambigous <multitype:, array>
     */
    public function getActiveDeliveryExecutions($userUri);
    
    /**
     * Returns all finished Delivery Executions of a User
     *
     * @param unknown $userUri
     * @return Ambigous <multitype:, array>
     */
    public function getFinishedDeliveryExecutions($userUri);
    
    /**
     * Generate a new delivery execution
     *
     * @param core_kernel_classes_Resource $compiled
     * @param string $userUri
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $compiled, $userUri);
    
    /**
     * Finishes a delivery execution
     *
     * @param core_kernel_classes_Resource $deliveryExecution
     * @return boolean success
     */
    public function finishDeliveryExecution(taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution);
    
    /**
     * Returns the delviery execution instance associated to the implementation 
     * 
     * @param string $identifier
     * @return taoDelivery_models_classes_execution_DeliveryExecution
     */
    public function getDeliveryExecution($identifier);    
}
