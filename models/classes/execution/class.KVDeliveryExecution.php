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

use oat\taoDelivery\models\classes\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecution as InterfaceDeliveryExecution;
use oat\taoDelivery\model\execution\KeyValueService;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 *
 */
class taoDelivery_models_classes_execution_KVDeliveryExecution implements InterfaceDeliveryExecution, \JsonSerializable
{
    /**
     * @var KeyValueService
     */
    private $service;

    private $id;

    private $data;

    public function __construct(KeyValueService $service, $identifier, $data = null)
    {
        $this->service = $service;
        $this->id = $identifier;
        $this->data = $data;
    }

    /**
     * (non-PHPdoc)
     *
     * @see InterfaceDeliveryExecution::getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * (non-PHPdoc)
     *
     * @see InterfaceDeliveryExecution::getStartTime()
     */
    public function getStartTime()
    {
        return $this->getData(PROPERTY_DELVIERYEXECUTION_START);
    }

    /**
     * (non-PHPdoc)
     *
     * @see InterfaceDeliveryExecution::getFinishTime()
     */
    public function getFinishTime()
    {
        try {
            return $this->getData(PROPERTY_DELVIERYEXECUTION_END);
        } catch (common_exception_NotFound $missingException) {
            return null;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see InterfaceDeliveryExecution::getLabel()
     */
    public function getLabel()
    {
        return $this->getData(RDFS_LABEL);
    }

    /**
     * (non-PHPdoc)
     *
     * @see InterfaceDeliveryExecution::getState()
     */
    public function getState()
    {
        return new core_kernel_classes_Resource($this->getData(PROPERTY_DELVIERYEXECUTION_STATUS));
    }

    /**
     * (non-PHPdoc)
     *
     * @see InterfaceDeliveryExecution::getDelivery()
     */
    public function getDelivery()
    {
        return new core_kernel_classes_Resource($this->getData(PROPERTY_DELVIERYEXECUTION_DELIVERY));
    }

    /**
     * (non-PHPdoc)
     *
     * @see InterfaceDeliveryExecution::getUserIdentifier()
     */
    public function getUserIdentifier()
    {
        return $this->getData(PROPERTY_DELVIERYEXECUTION_SUBJECT);
    }

    /**
     * (non-PHPdoc)
     * @see InterfaceDeliveryExecution::setState()
     */
    public function setState($state)
    {
        $oldState = $this->getState()->getUri();
        if ($oldState == $state) {
            common_Logger::w('Delivery execution ' . $this->getIdentifier() . ' already in state ' . $state);
            return false;
        }
        $this->setData(PROPERTY_DELVIERYEXECUTION_STATUS, $state);
        if ($state == InterfaceDeliveryExecution::STATE_FINISHIED) {
            $this->setData(PROPERTY_DELVIERYEXECUTION_END, microtime());
        }
        return $this->service->updateDeliveryExecutionStatus($this, $oldState, $state);
    }

    private function getData($dataKey)
    {
        if (is_null($this->data)) {
            $this->data = $this->service->getData($this->id);
        }
        if (! isset($this->data[$dataKey])) {
            throw new common_exception_NotFound('Information ' . $dataKey . ' not found for entry ' . $this->id);
        }
        return $this->data[$dataKey];
    }

    private function setData($dataKey, $value)
    {
        if (is_null($this->data)) {
            $this->data = $this->service->getData($this->id);
        }
        $this->data[$dataKey] = $value;
    }

    /**
     * (non-PHPdoc)
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        if (is_null($this->data)) {
            throw new common_exception_Error('Unloaded delivery execution serialized');
        }
        return $this->data;
    }

    /**
     * Stored the current data
     */
    private function save()
    {
        $this->service->update($this);
    }
}
