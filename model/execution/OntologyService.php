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

use common_Logger;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDeleteRequest;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class OntologyService extends ConfigurableService implements DeliveryExecutionService, Monitoring
{

    /**
     * (non-PHPdoc)
     * @see Monitoring::getExecutionsByDelivery()
     * @param core_kernel_classes_Resource $compiled
     * @return DeliveryExecution[]
     */
    public function getExecutionsByDelivery(core_kernel_classes_Resource $compiled)
    {
        $executionClass = new core_kernel_classes_Class(OntologyDeliveryExecution::CLASS_URI);
        $resources = $executionClass->searchInstances([
            OntologyDeliveryExecution::PROPERTY_DELIVERY => $compiled->getUri()
        ], [
            'like' => false
        ]);
        $returnValue = [];
        foreach ($resources as $resource) {
            $returnValue[] = $this->getDeliveryExecution($resource);
        }
        return $returnValue;
    }

    public function getDeliveryExecutionsByStatus($userUri, $status)
    {
        $executionClass = new core_kernel_classes_Class(OntologyDeliveryExecution::CLASS_URI);
        $started = $executionClass->searchInstances([
            OntologyDeliveryExecution::PROPERTY_SUBJECT => $userUri,
            OntologyDeliveryExecution::PROPERTY_STATUS => $status
        ], [
            'like' => false
        ]);
        $returnValue = [];
        foreach ($started as $resource) {
            $returnValue[] = $this->getDeliveryExecution($resource);
        }
        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see DeliveryExecutionService::getUserExecutions()
     */
    public function getUserExecutions(core_kernel_classes_Resource $compiled, $userUri)
    {
        $executionClass = new core_kernel_classes_Class(OntologyDeliveryExecution::CLASS_URI);
        $instances = $executionClass->searchInstances([
            OntologyDeliveryExecution::PROPERTY_SUBJECT  => $userUri,
            OntologyDeliveryExecution::PROPERTY_DELIVERY => $compiled->getUri()
        ], [
            'like' => false
        ]);
        $deliveryExecutions = [];
        foreach ($instances as $resource) {
            $deliveryExecutions[] = $this->getDeliveryExecution($resource->getUri());
        }
        return $deliveryExecutions;
    }

    /**
     * @deprecated
     * (non-PHPdoc)
     * @see DeliveryExecutionService::initDeliveryExecution()
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $userUri)
    {
        common_Logger::w('Call to deprecated function ' . __FUNCTION__);
        return $this->spawnDeliveryExecution(
            $assembly->getLabel(),
            $assembly->getUri(),
            $userUri,
            DeliveryExecution::STATE_ACTIVE
        );
    }

    /**
     * Spawn a new Delivery Execution
     *
     * @param string $label
     * @param string $deliveryId
     * @param string $userId
     * @param string $status
     * @return \oat\taoDelivery\model\execution\DeliveryExecution
     */
    public function spawnDeliveryExecution($label, $deliveryId, $userId, $status)
    {
        $executionClass = new core_kernel_classes_Class(OntologyDeliveryExecution::CLASS_URI);
        $execution = $executionClass->createInstanceWithProperties([
            OntologyRdfs::RDFS_LABEL              => $label,
            OntologyDeliveryExecution::PROPERTY_DELIVERY             => $deliveryId,
            OntologyDeliveryExecution::PROPERTY_SUBJECT    => $userId,
            OntologyDeliveryExecution::PROPERTY_TIME_START      => microtime(),
            OntologyDeliveryExecution::PROPERTY_STATUS     => $status
        ]);
        return $this->getDeliveryExecution($execution);
    }

    /**
     * (non-PHPdoc)
     * @see DeliveryExecutionService::getDeliveryExecution()
     */
    public function getDeliveryExecution($identifier)
    {
        return $this->propagate(new DeliveryExecution(
            new OntologyDeliveryExecution($identifier)
        ));
    }

    /**
     * @inheritdoc
     */
    public function deleteDeliveryExecutionData(DeliveryExecutionDeleteRequest $request)
    {
        $resource = new core_kernel_classes_Resource($request->getDeliveryExecution()->getIdentifier());

        return $resource->delete();
    }
}
