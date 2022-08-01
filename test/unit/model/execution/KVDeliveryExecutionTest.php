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

use core_kernel_classes_Resource;
use oat\generis\model\OntologyRdfs;
use oat\taoDelivery\model\execution\DeliveryExecutionMetadataInterface;
use oat\taoDelivery\model\execution\exception\NonExistentMetadataException;
use oat\taoDelivery\model\execution\implementation\KeyValueService;
use oat\taoDelivery\model\execution\KVDeliveryExecution;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use PHPUnit\Framework\TestCase;

class KVDeliveryExecutionTest extends TestCase
{

    private const DATASET = [
        OntologyDeliveryExecution::PROPERTY_TIME_END => '1234',
        OntologyDeliveryExecution::PROPERTY_TIME_START => '4321',
        OntologyRdfs::RDFS_LABEL => 'label',
        OntologyDeliveryExecution::PROPERTY_STATUS => 'tao.example.status.uri',
        OntologyDeliveryExecution::PROPERTY_DELIVERY => 'tao.delivery.example.uri',
        OntologyDeliveryExecution::PROPERTY_SUBJECT => 'subject',
        DeliveryExecutionMetadataInterface::PROPERTY_METADATA => [
            'metadata1' => 'metadata value 1'
        ]
    ];

    private KeyValueService $keyValueServiceMock;

    public function setUp(): void
    {
        $this->keyValueServiceMock = $this->createMock(KeyValueService::class);
    }

    public function testGettersWitchData(): void
    {
        $this->assertDataset(new KVDeliveryExecution($this->keyValueServiceMock, 'id', self::DATASET));
    }

    public function testGetterWithService()
    {
        $this->keyValueServiceMock->method('getData')->willReturn(self::DATASET);
        $this->assertDataset(new KVDeliveryExecution($this->keyValueServiceMock, 'id'));
    }

    private function assertDataset(KVDeliveryExecution $subject)
    {
        self::assertEquals('id', $subject->getIdentifier());
        self::assertEquals('1234', $subject->getFinishTime());
        self::assertEquals('label', $subject->getLabel());
        self::assertEquals('subject', $subject->getUserIdentifier());
        self::assertInstanceOf(core_kernel_classes_Resource::class, $subject->getState());
        self::assertEquals($subject->getState()->getUri(), 'tao.example.status.uri');
        self::assertInstanceOf(core_kernel_classes_Resource::class, $subject->getDelivery());
        self::assertEquals($subject->getDelivery()->getUri(), 'tao.delivery.example.uri');
        self::assertEquals($subject->getAllMetadata(), ['metadata1' => 'metadata value 1']);
        self::assertEquals($subject->getMetadata('metadata1'), 'metadata value 1');
    }

    public function testAddMetadataToExistingArray()
    {
        $this->keyValueServiceMock->expects(self::once())->method('update');
        $subject = new KVDeliveryExecution($this->keyValueServiceMock, 'id', self::DATASET);
        $subject->addMetadata(['metadata2' => 'metadata content 2']);
        self::assertEquals($subject->getMetadata('metadata2'), 'metadata content 2');
    }

    public function testAddMetadataToEmptyMetadata()
    {
        $this->keyValueServiceMock->expects(self::once())->method('update');
        $subject = new KVDeliveryExecution($this->keyValueServiceMock, 'id',[]);
        $subject->addMetadata(['metadata2' => 'metadata content 2']);
        self::assertEquals($subject->getMetadata('metadata2'), 'metadata content 2');
    }

    public function testGetMetadataWhenArrayDoesNotExist()
    {
        $this->expectException(NonExistentMetadataException::class);
        $subject = new KVDeliveryExecution($this->keyValueServiceMock, 'id', self::DATASET);
        $subject->getMetadata('metadata2');
    }
}
