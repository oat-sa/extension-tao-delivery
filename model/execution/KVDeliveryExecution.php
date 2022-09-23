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

use common_exception_Error;
use common_exception_NotFound;
use common_Logger;
use core_kernel_classes_Resource;
use oat\generis\model\OntologyRdfs;
use oat\taoDelivery\model\execution\implementation\KeyValueService;
use oat\taoDelivery\model\execution\metadata\Metadata;
use oat\taoDelivery\model\execution\metadata\MetadataCollection;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 *
 */
class KVDeliveryExecution implements DeliveryExecutionMetadataInterface, OriginalIdAwareDeliveryExecutionInterface
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
     * @see DeliveryExecutionInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getOriginalIdentifier(): string
    {
        return substr($this->id, strlen(KeyValueService::DELIVERY_EXECUTION_PREFIX));
    }

    /**
     * (non-PHPdoc)
     *
     * @see DeliveryExecutionInterface::getStartTime()
     */
    public function getStartTime()
    {
        return $this->getData(OntologyDeliveryExecution::PROPERTY_TIME_START);
    }

    /**
     * (non-PHPdoc)
     *
     * @see DeliveryExecutionInterface::getFinishTime()
     */
    public function getFinishTime()
    {
        if ($this->hasData(OntologyDeliveryExecution::PROPERTY_TIME_END)) {
            return $this->getData(OntologyDeliveryExecution::PROPERTY_TIME_END);
        }
        return null;
    }

    /**
     * (non-PHPdoc)
     *
     * @see DeliveryExecutionInterface::getLabel()
     */
    public function getLabel()
    {
        return $this->getData(OntologyRdfs::RDFS_LABEL);
    }

    /**
     * (non-PHPdoc)
     *
     * @see DeliveryExecutionInterface::getState()
     */
    public function getState()
    {
        return new core_kernel_classes_Resource($this->getData(OntologyDeliveryExecution::PROPERTY_STATUS));
    }

    /**
     * (non-PHPdoc)
     *
     * @see DeliveryExecutionInterface::getDelivery()
     */
    public function getDelivery()
    {
        return new core_kernel_classes_Resource($this->getData(OntologyDeliveryExecution::PROPERTY_DELIVERY));
    }

    /**
     * (non-PHPdoc)
     *
     * @see DeliveryExecutionInterface::getUserIdentifier()
     */
    public function getUserIdentifier()
    {
        return $this->getData(OntologyDeliveryExecution::PROPERTY_SUBJECT);
    }

    /**
     * (non-PHPdoc)
     * @see DeliveryExecutionInterface::setState()
     */
    public function setState($state)
    {
        $oldState = $this->getState()->getUri();
        if ($oldState == $state) {
            common_Logger::w('Delivery execution ' . $this->getIdentifier() . ' already in state ' . $state);
            return false;
        }
        $this->setData(OntologyDeliveryExecution::PROPERTY_STATUS, $state);
        if ($state == DeliveryExecutionInterface::STATE_FINISHED) {
            $this->setData(OntologyDeliveryExecution::PROPERTY_TIME_END, microtime());
        }
        return $this->service->updateDeliveryExecutionStatus($this, $oldState, $state);
    }

    private function getData($dataKey)
    {
        if (is_null($this->data)) {
            $this->data = $this->service->getData($this->id);
        }
        if (!isset($this->data[$dataKey])) {
            throw new common_exception_NotFound('Information ' . $dataKey . ' not found for entry ' . $this->id);
        }
        return $this->data[$dataKey];
    }

    private function hasData($dataKey)
    {
        if (is_null($this->data)) {
            $this->data = $this->service->getData($this->id);
        }
        return isset($this->data[$dataKey]);
    }

    private function setData($dataKey, $value): void
    {
        if (is_null($this->data)) {
            $this->data = $this->service->getData($this->id);
        }
        $this->data[$dataKey] = $value;
    }

    /**
     * (non-PHPdoc)
     * @throws common_exception_Error
     */
    public function jsonSerialize(): array
    {
        if ($this->data === null) {
            $this->retryDeliveryExecutionLoad();
        }

        return $this->data;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->service->exists($this->id);
    }

    /**
     * Stored the current data
     *
     * @deprecated since version 13.1.0 - not used anywhere
     */
    private function save()
    {
        $this->service->update($this);
    }

    public function getAllMetadata(): MetadataCollection
    {
        $collection = new MetadataCollection();
        try {
            $this->extractCollection($collection);
        } catch (common_exception_NotFound $exception) {
            common_Logger::w($exception->getMessage());
        }
        return $collection;
    }

    public function addMetadata(Metadata $metadata): void
    {
        try {
            $collection = $this->getAllMetadata()->addMetadata($metadata);
            $this->setData(
                DeliveryExecutionMetadataInterface::PROPERTY_METADATA,
                $collection->jsonSerialize()
            );
        } catch (common_exception_NotFound $exception) {
            $this->setData(
                DeliveryExecutionMetadataInterface::PROPERTY_METADATA,
                new MetadataCollection($metadata)
            );
        }
    }

    public function getMetadata(string $metadataId): ?Metadata
    {
        return $this->getAllMetadata()->getMetadataElement($metadataId);
    }

    /**
     * @throws common_exception_NotFound
     */
    private function extractCollection(MetadataCollection $collection): void
    {
        foreach ($this->getData(DeliveryExecutionMetadataInterface::PROPERTY_METADATA) as $metadata) {
            if (
                !($metadata instanceof Metadata)
                && isset($metadata[Metadata::METADATA_ID], $metadata[Metadata::METADATA_CONTENT])
            ) {
                $collection->addMetadata(
                    new Metadata($metadata[Metadata::METADATA_ID], $metadata[Metadata::METADATA_CONTENT])
                );
                continue;
            }
            $collection->addMetadata($metadata);
        }
    }

    /**
     * @throws common_exception_Error
     */
    private function retryDeliveryExecutionLoad(): void
    {
        $this->data = $this->service->getData($this->getIdentifier());

        if ($this->data === null) {
            throw new common_exception_Error('Delivery not found');
        }
    }
}
