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
 * @subpackage models_classes
 */
class taoDelivery_models_classes_execution_ServiceProxy extends tao_models_classes_GenerisService
    implements taoDelivery_models_classes_execution_Service
{
    /**
     * @var taoDelivery_models_classes_execution_Service
     */
    private $implementation;
    
    /**
     * protected constructor for singleton pattern
     */
    protected function __construct() {
        $this->implementation = taoDelivery_models_classes_execution_OntologyService::singleton();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getTotalExecutionCount()
     */
    public function getTotalExecutionCount(core_kernel_classes_Resource $compiled)
    {
        return $this->implementation->getTotalExecutionCount($compiled);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getUserExecutionCount()
     */
    public function getUserExecutionCount(core_kernel_classes_Resource $assembly, $userUri) {
        return $this->implementation->getUserExecutionCount($assembly, $userUri);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getActiveDeliveryExecutions()
     */
    public function getActiveDeliveryExecutions($userUri)
    {
        return $this->implementation->getActiveDeliveryExecutions($userUri);
    }
        
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getFinishedDeliveryExecutions()
     */
    public function getFinishedDeliveryExecutions($userUri)
    {
        return $this->implementation->getFinishedDeliveryExecutions($userUri);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::initDeliveryExecution()
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $compiled, $userUri)
    {
        return $this->implementation->initDeliveryExecution($compiled, $userUri);
    }
    
   /**
    * (non-PHPdoc)
    * @see taoDelivery_models_classes_execution_Service::finishDeliveryExecution()
    */
    public function finishDeliveryExecution(taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution)
    {
        return $this->implementation->finishDeliveryExecution($deliveryExecution);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecution()
     */
    public function getDeliveryExecution($identifier)
    {
        return $this->implementation->getDeliveryExecution($identifier);
    }    

}
