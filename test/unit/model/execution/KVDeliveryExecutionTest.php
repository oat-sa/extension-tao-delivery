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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoDelivery\test\unit\model\execution;

use common_exception_NotFound;
use core_kernel_classes_Resource;
use oat\generis\model\OntologyRdfs;
use oat\taoDelivery\model\execution\DeliveryExecutionMetadataInterface;
use oat\taoDelivery\model\execution\exception\NonExistentMetadataException;
use oat\taoDelivery\model\execution\implementation\KeyValueService;
use oat\taoDelivery\model\execution\KVDeliveryExecution;
use oat\taoDelivery\model\execution\metadata\Metadata;
use oat\taoDelivery\model\execution\metadata\MetadataCollection;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use PHPUnit\Framework\TestCase;

class KVDeliveryExecutionTest extends TestCase
{
    private KeyValueService $keyValueServiceMock;

    public function setUp(): void
    {
        $this->keyValueServiceMock = $this->createMock(KeyValueService::class);
        $this->dataset = [
            OntologyDeliveryExecution::PROPERTY_TIME_END => '1234',
            OntologyDeliveryExecution::PROPERTY_TIME_START => '4321',
            OntologyRdfs::RDFS_LABEL => 'label',
            OntologyDeliveryExecution::PROPERTY_STATUS => 'tao.example.status.uri',
            OntologyDeliveryExecution::PROPERTY_DELIVERY => 'tao.delivery.example.uri',
            OntologyDeliveryExecution::PROPERTY_SUBJECT => 'subject',
            DeliveryExecutionMetadataInterface::PROPERTY_METADATA => [
                'some_metadataId' => [
                    'metadataId' => 'some_metadataId',
                    'metadataContent' => 'some content'
                ]
            ]
        ];
    }

    public function testGettersWitchData(): void
    {
        $this->assertDataset(new KVDeliveryExecution($this->keyValueServiceMock, 'id', $this->dataset));
    }

    public function testGetterWithService(): void
    {
        $this->keyValueServiceMock->method('getData')->willReturn($this->dataset);
        $this->assertDataset(new KVDeliveryExecution($this->keyValueServiceMock, 'id'));
    }

    private function assertDataset(KVDeliveryExecution $subject): void
    {
        self::assertEquals('id', $subject->getIdentifier());
        self::assertEquals('1234', $subject->getFinishTime());
        self::assertEquals('label', $subject->getLabel());
        self::assertEquals('subject', $subject->getUserIdentifier());
        self::assertInstanceOf(core_kernel_classes_Resource::class, $subject->getState());
        self::assertEquals($subject->getState()->getUri(), 'tao.example.status.uri');
        self::assertInstanceOf(core_kernel_classes_Resource::class, $subject->getDelivery());
        self::assertEquals($subject->getDelivery()->getUri(), 'tao.delivery.example.uri');

        self::assertEquals(
            new MetadataCollection(new Metadata('some_metadataId', 'some content')),
            $subject->getAllMetadata()
        );

        self::assertEquals(
            new Metadata('some_metadataId', 'some content'),
            $subject->getMetadata('some_metadataId')
        );
    }

    public function testAddMetadataToExistingArray(): void
    {
        $subject = new KVDeliveryExecution($this->keyValueServiceMock, 'id', $this->dataset);
        $subject->addMetadata(new Metadata('metadata2', 'metadata content 2'));
        self::assertEquals(
            new Metadata('metadata2', 'metadata content 2'),
            $subject->getMetadata('metadata2')
        );
    }

    public function testAddMetadataToEmptyMetadata(): void
    {
        $subject = new KVDeliveryExecution($this->keyValueServiceMock, 'id', []);
        $subject->addMetadata(new Metadata('metadata2', 'metadata content 2'));
        self::assertEquals(
            new Metadata('metadata2', 'metadata content 2'),
            $subject->getMetadata('metadata2')
        );
    }

    public function testGetMetadataWhenArrayDoesNotExist(): void
    {
        $subject = new KVDeliveryExecution($this->keyValueServiceMock, 'id', $this->dataset);
        $subject->getMetadata('metadata2');
        self::assertNull($subject->getMetadata('metadata2'));
    }

    public function getNonExistentPropertyWillThrowError(): void
    {
        $this->expectException(common_exception_NotFound::class);
        $subject = new KVDeliveryExecution($this->keyValueServiceMock, 'id', []);
        $subject->getState();
    }
}
