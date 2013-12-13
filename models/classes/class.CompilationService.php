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
 * returns the folder to store the compiled delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_CompilationService extends taoDelivery_models_classes_DeliveryService
{

    /**
     * Returns the last compilation of the delivery
     * Or null if no compilation is found
     * 
     * @param core_kernel_classes_Resource $delivery
     * @return core_kernel_classes_Resource
     */
    public function getActiveCompilation(core_kernel_classes_Resource $delivery) {
        return $delivery->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_DELIVERY_ACTIVE_COMPILATION));
    }
    
    public function getAllCompilations(core_kernel_classes_Resource $delivery) {
        $compilationClass = new core_kernel_classes_Class(CLASS_COMPILEDDELIVERY);
        return $compilationClass->searchInstances(array(
            PROPERTY_COMPILEDDELIVERY_DELIVERY => $delivery,
        ),array(
        	'like' => 'false'
        ));
    }
    
    public function compileDelivery(core_kernel_classes_Resource $delivery) {
        $content = $this->getContent($delivery);
        if (is_null($content)) {
            throw new taoDelivery_models_classes_EmptyDeliveryException('Delivery has no content');
        }
        
        $compilationClass = new core_kernel_classes_Class(CLASS_COMPILEDDELIVERY);
        $compilationInstance = $compilationClass->createInstanceWithProperties(array(
            RDFS_LABEL                         => $delivery->getLabel(),
            PROPERTY_COMPILEDDELIVERY_DELIVERY => $delivery,
        ));        
        
        try {
            $compiler = taoDelivery_models_classes_DeliveryCompiler::createCompiler($content);
            $serviceCall = $compiler->compile();
            $compilationInstance->setPropertiesValues(array(
                PROPERTY_COMPILEDDELIVERY_DIRECTORY => $compiler->getSpawnedDirectoryIds(),
                PROPERTY_COMPILEDDELIVERY_TIME      => time(),
                PROPERTY_COMPILEDDELIVERY_RUNTIME   => $serviceCall->toOntology()
            ));
            $delivery->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERY_ACTIVE_COMPILATION), $compilationInstance);
        } catch (common_Exception $e) {
            $compilationInstance->delete();
            if ($e instanceof tao_models_classes_CompilationFailedException) {
                throw $e;
            } else {
                throw new taoDelivery_models_classes_CompilationFailedException('Compilation failed: '.$e->getMessage());
            }
        }
        
        return true;
    }
    
    public function getCompilerClass(core_kernel_classes_Resource $deliveryContent) {
        return $this->getImplementationByContent($deliveryContent)->getCompilerClass();
    }
    
    /**
     * Returns the date of the compilation of a delivery
     * 
     * @param core_kernel_classes_Resource $compiledDelivery
     * @return string
     */
    public function getCompilationDate( core_kernel_classes_Resource $compiledDelivery) {
        return (string)$compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_TIME));
    }
        
    /**
     * Gets the service call to run this delivery
     * 
     * @param core_kernel_classes_Resource $compiledDelivery
     * @return tao_models_classes_service_ServiceCall
     */
    public function getCompilationRuntime( core_kernel_classes_Resource $compiledDelivery) {
        $runtimeResource = $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
        return tao_models_classes_service_ServiceCall::fromResource($runtimeResource);
    }
    
    public function getRuntime( core_kernel_classes_Resource $compiledDelivery, $variables = array()) {
        return $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
    }

}