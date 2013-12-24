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
class taoDelivery_models_classes_execution_OntologyService extends tao_models_classes_GenerisService
    implements taoDelivery_models_classes_execution_Service
{
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getTotalExecutionCount()
     */
    public function getTotalExecutionCount(core_kernel_classes_Resource $compiled)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $count = $executionClass->countInstances(array(
            PROPERTY_DELVIERYEXECUTION_DELIVERY => $compiled->getUri()
        ), array(
            'like' => false
        ));
        return $count;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getUserExecutionCount()
     */
    public function getUserExecutionCount(core_kernel_classes_Resource $compiled, $userUri)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $count = $executionClass->countInstances(array(
            PROPERTY_DELVIERYEXECUTION_SUBJECT  => $userUri,
            PROPERTY_DELVIERYEXECUTION_DELIVERY => $compiled->getUri()
        ), array(
            'like' => false
        ));
        return $count;
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getActiveDeliveryExecutions()
     */
    public function getActiveDeliveryExecutions($userUri)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $started = $executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_SUBJECT => $userUri,
            PROPERTY_DELVIERYEXECUTION_STATUS => INSTANCE_DELIVERYEXEC_ACTIVE
        ), array(
            'like' => false
        ));
        $returnValue = array();
        foreach ($started as $resource) {
            $returnValue[] = $this->getDeliveryExecution($resource);
        }
        return $returnValue;
    }
        
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getFinishedDeliveryExecutions()
     */
    public function getFinishedDeliveryExecutions($userUri)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $finished = $executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_SUBJECT => $userUri,
            PROPERTY_DELVIERYEXECUTION_STATUS => INSTANCE_DELIVERYEXEC_FINISHED
        ), array(
            'like' => false
        ));
         $returnValue = array();
        foreach ($finished as $resource) {
            $returnValue[] = $this->getDeliveryExecution($resource);
        }
        return $returnValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::initDeliveryExecution()
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $compiled, $userUri)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $execution = $executionClass->createInstanceWithProperties(array(
            RDFS_LABEL                            => $compiled->getLabel(),
            PROPERTY_DELVIERYEXECUTION_DELIVERY   => $compiled,
            PROPERTY_DELVIERYEXECUTION_SUBJECT    => $userUri,
            PROPERTY_DELVIERYEXECUTION_START      => time(),
            PROPERTY_DELVIERYEXECUTION_STATUS     => INSTANCE_DELIVERYEXEC_ACTIVE        	
        ));
        return $this->getDeliveryExecution($execution);
    }
    
   /**
    * (non-PHPdoc)
    * @see taoDelivery_models_classes_execution_Service::finishDeliveryExecution()
    */
    public function finishDeliveryExecution(taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution)
    {
        if (!$deliveryExecution instanceof taoDelivery_models_classes_execution_OntologyDeliveryExecution) {
            throw new common_exception_Error('Delviery Execution Implementation mismatch, got '.get_class($deliveryExecution).' in '.__CLASS__);
        }
        $statusProp = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS);
        $currentStatus = $deliveryExecution->getUniquePropertyValue($statusProp);
        if ($currentStatus->getUri() == INSTANCE_DELIVERYEXEC_FINISHED) {
            throw new common_Exception('Delivery execution '.$deliveryExecution->getUri().' has laready been finished');
        }
        $deliveryExecution->editPropertyValues($statusProp, INSTANCE_DELIVERYEXEC_FINISHED);
        $deliveryExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_END), time());
        return true;
    }
        
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecution()
     */
    public function getDeliveryExecution($identifier) {
        return new taoDelivery_models_classes_execution_OntologyDeliveryExecution($identifier);
    }
}
