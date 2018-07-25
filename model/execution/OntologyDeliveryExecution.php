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
 * Copyright (c) 2013-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\taoDelivery\model\execution;

use common_exception_NotFound;
use common_Logger;
use core_kernel_classes_Resource;


/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
class OntologyDeliveryExecution extends core_kernel_classes_Resource implements DeliveryExecutionInterface
{
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecution';
    
    const PROPERTY_DELIVERY = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionDelivery';
    
    const PROPERTY_SUBJECT = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionSubject';
    
    const PROPERTY_TIME_START = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStart';
    
    const PROPERTY_TIME_END = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionEnd';
    
    const PROPERTY_STATUS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#StatusOfDeliveryExecution';

    private $startTime;
    private $finishTime;
    private $state;
    private $delivery;
    private $userIdentifier;

    /**
     * (non-PHPdoc)
     * @see DeliveryExecution::getIdentifier()
     */
    public function getIdentifier() {
        return $this->getUri();
    }

    /**
     * (non-PHPdoc)
     * @see DeliveryExecution::getStartTime()
     * @throws \common_exception_NotFound
     */
    public function getStartTime() {
        if (!isset($this->startTime)) {
            $this->startTime = (string)$this->getData(OntologyDeliveryExecution::PROPERTY_TIME_START);
        }
        return $this->startTime;
    }

    
    /**
     * (non-PHPdoc)
     * @see DeliveryExecution::getFinishTime()
     */
    public function getFinishTime() {
        if (!isset($this->finishTime)) {
            try {
                $this->finishTime = (string)$this->getData(OntologyDeliveryExecution::PROPERTY_TIME_END);
            } catch (common_exception_NotFound $missingException) {
                $this->finishTime = null;
            }
        }
        return $this->finishTime;
    }
    
    /**
     * (non-PHPdoc)
     * @see DeliveryExecution::getState()
     */
    public function getState() {
        if (!isset($this->state)) {
            $state = $this->getData(OntologyDeliveryExecution::PROPERTY_STATUS);
            if (!$state instanceof core_kernel_classes_Resource) {
                $state = $this->getResource((string)$state);
            }
            $this->state = $state;
        }
        return $this->state;
    }
    
    /**
     * (non-PHPdoc)
     * @see DeliveryExecution::getDelivery()
     */
    public function getDelivery() {
        if (!isset($this->delivery)) {
            $this->delivery = $this->getData(OntologyDeliveryExecution::PROPERTY_DELIVERY);
        }
        return $this->delivery;
    }
    
    /**
     * (non-PHPdoc)
     * @see DeliveryExecution::getUserIdentifier()
     */
    public function getUserIdentifier() {
        if (!isset($this->userIdentifier)) {
            $user = $this->getData(OntologyDeliveryExecution::PROPERTY_SUBJECT);
            $this->userIdentifier =  ($user instanceof core_kernel_classes_Resource) ? $user->getUri() : (string)$user;
        }
        return $this->userIdentifier;
    }
    
    /**
     * (non-PHPdoc)
     * @see DeliveryExecution::setState()
     */
    public function setState($state) {
        $statusProp = $this->getProperty(OntologyDeliveryExecution::PROPERTY_STATUS);
        $state = $this->getResource($state);
        $currentStatus = $this->getState();
        if ($currentStatus->getUri() == $state->getUri()) {
            common_Logger::w('Delivery execution '.$this->getIdentifier().' already in state '.$state->getUri());
            return false;
        }
        $this->editPropertyValues($statusProp, $state);
        if ($state->getUri() == DeliveryExecutionInterface::STATE_FINISHED) {
            $this->setPropertyValue($this->getProperty(OntologyDeliveryExecution::PROPERTY_TIME_END), microtime());
        }
        $this->state = $state;
        return true;
    }

    /**
     * @param $propertyName
     * @return \core_kernel_classes_Container
     * @throws common_exception_NotFound
     */
    private function getData($propertyName){
        $property = $this->getProperty($propertyName);
        $propertyValue = $this->getOnePropertyValue($property);
        if(is_null($propertyValue)){
            throw new common_exception_NotFound('Property '.$propertyName.' not found for resource ' . $this->getUri());
        }

        return $propertyValue;
    }
}
