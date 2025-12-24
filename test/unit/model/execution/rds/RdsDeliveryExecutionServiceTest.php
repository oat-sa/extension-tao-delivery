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
 * Copyright (c) 2013-2022 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDelivery\test\unit\model\execution\rds;

use common_persistence_Persistence;
use core_kernel_classes_Resource;
use oat\generis\persistence\PersistenceManager;
use oat\generis\test\ServiceManagerMockTrait;
use oat\generis\test\SqlMockTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDeleteRequest;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecutionService;
use oat\taoDelivery\scripts\install\GenerateRdsDeliveryExecutionTable;

class RdsDeliveryExecutionServiceTest extends TestCase
{
    use SqlMockTrait;
    use ServiceManagerMockTrait;

    private RdsDeliveryExecutionService|MockObject $classUnderTest;
    private common_persistence_Persistence $persistence;

    public function setUp(): void
    {
        $managerMock = $this->getSqlMock("default");
        $serviceLocatorMock = $this->getServiceManagerMock([
            PersistenceManager::SERVICE_ID => $managerMock,
        ]);

        $this->persistence = $managerMock->getPersistenceById("default");

        $rdsHelper = new GenerateRdsDeliveryExecutionTable();
        $rdsHelper->generateTable($this->persistence);

        $this->classUnderTest = $this
            ->getMockBuilder(RdsDeliveryExecutionService::class)
            ->onlyMethods(["getNewUri"])
            ->getMock();

        $this->classUnderTest->setServiceLocator($serviceLocatorMock);
        $this->classUnderTest->method("getNewUri")->willReturn("test");
    }

    public function testClassMethods(): void
    {
        $this->assertTrue(method_exists($this->classUnderTest, "deleteDeliveryExecutionData"));
        $this->assertTrue(method_exists($this->classUnderTest, "getExecutionsByDelivery"));
        $this->assertTrue(method_exists($this->classUnderTest, "getUserExecutions"));
        $this->assertTrue(method_exists($this->classUnderTest, "getDeliveryExecutionsByStatus"));
        $this->assertTrue(method_exists($this->classUnderTest, "spawnDeliveryExecution"));
        $this->assertTrue(method_exists($this->classUnderTest, "initDeliveryExecution"));
        $this->assertTrue(method_exists($this->classUnderTest, "getDeliveryExecution"));
    }

    public function testClassContants(): void
    {
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::ID_PREFIX"));
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::TABLE_NAME"));
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::COLUMN_ID"));
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::COLUMN_DELIVERY_ID"));
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::COLUMN_USER_ID"));
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::COLUMN_STATUS"));
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::COLUMN_FINISHED_AT"));
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::COLUMN_STARTED_AT"));
        $this->assertTrue(defined(RdsDeliveryExecutionService::class . "::COLUMN_LABEL"));
    }

    public function testDeleteDeliveryExecutionData(): void
    {
        $deliveryExecutionMock = $this->createMock(DeliveryExecution::class);
        $deliveryExecutionMock->method('getIdentifier')->willReturn("test");

        $requestMock = $this->createMock(DeliveryExecutionDeleteRequest::class);
        $requestMock->method('getDeliveryExecution')->willReturn($deliveryExecutionMock);

        $result = $this->classUnderTest->deleteDeliveryExecutionData($requestMock);

        $this->assertIsInt($result);
        $this->assertSame($result, 0);
    }

    public function testDeleteDeliveryExecutionDataWithNonEmptyDatabase(): void
    {
        $this->insertNewRow();

        $deliveryExecutionMock = $this->createMock(DeliveryExecution::class);
        $deliveryExecutionMock->method('getIdentifier')->willReturn("test");

        $requestMock = $this->createMock(DeliveryExecutionDeleteRequest::class);
        $requestMock->method('getDeliveryExecution')->willReturn($deliveryExecutionMock);

        $result = $this->classUnderTest->deleteDeliveryExecutionData($requestMock);

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testGetExecutionsByDeliveryWithEmptyDatabase(): void
    {
        $resource = $this->createResource();

        $executions = $this->classUnderTest->getExecutionsByDelivery($resource);

        $this->assertIsArray($executions);
        $this->assertCount(0, $executions);
    }

    public function testGetExecutionsByDeliveryWithNonEmptyDatabase(): void
    {
        $this->insertNewRow();

        $resourceMock = $this->createMock(core_kernel_classes_Resource::class);
        $resourceMock->method('getUri')->willReturn("test");

        $this->assertIsArray($this->classUnderTest->getExecutionsByDelivery($resourceMock));
        $this->assertCount(1, $this->classUnderTest->getExecutionsByDelivery($resourceMock));
    }

    public function testGetUserExecutions(): void
    {
        $resource = $this->createResource();

        $this->assertIsArray($this->classUnderTest->getUserExecutions($resource, "test"));
        $this->assertCount(0, $this->classUnderTest->getExecutionsByDelivery($resource));
    }

    public function testGetUserExecutionsWithNonEmptyDatabase(): void
    {
        $this->insertNewRow();

        $resourceMock = $this->createMock(core_kernel_classes_Resource::class);
        $resourceMock->method('getUri')->willReturn("test");

        $this->assertIsArray($this->classUnderTest->getUserExecutions($resourceMock, "test"));
        $this->assertCount(1, $this->classUnderTest->getUserExecutions($resourceMock, "test"));
    }

    public function testGetDeliveryExecutionsByStatus(): void
    {
        $this->assertIsArray($this->classUnderTest->getDeliveryExecutionsByStatus("test", "test"));
        $this->assertCount(0, $this->classUnderTest->getDeliveryExecutionsByStatus("test", "test"));
    }

    public function testGetDeliveryExecutionsByStatusWithNonEmptyDatabase(): void
    {
        $this->insertNewRow();

        $this->assertIsArray($this->classUnderTest->getDeliveryExecutionsByStatus("test", "test"));
        $this->assertCount(1, $this->classUnderTest->getDeliveryExecutionsByStatus("test", "test"));
    }

    public function testSpawnDeliveryExecution(): void
    {
        $this->assertInstanceOf(
            DeliveryExecution::class,
            $this->classUnderTest->spawnDeliveryExecution("test", "test", "test", "test", "test")
        );
    }

    public function testInitDeliveryExecution(): void
    {
        $resourceMock = $this->createMock(core_kernel_classes_Resource::class);

        $resourceMock->method('getLabel')->willReturn("test");
        $resourceMock->method('getUri')->willReturn("test");

        $this->assertInstanceOf(
            DeliveryExecution::class,
            $this->classUnderTest->initDeliveryExecution($resourceMock, "test")
        );
    }

    public function testGetDeliveryExecution(): void
    {
        $this->assertInstanceOf(DeliveryExecution::class, $this->classUnderTest->getDeliveryExecution("test"));
    }

    public function testGetPersistence(): void
    {
        $this->assertInstanceOf(\common_persistence_SqlPersistence::class, $this->classUnderTest->getPersistence());
    }

    private function insertNewRow(): void
    {
        $query = "INSERT INTO " . RdsDeliveryExecutionService::TABLE_NAME . " ("
            . RdsDeliveryExecutionService::COLUMN_ID . ", "
            . RdsDeliveryExecutionService::COLUMN_DELIVERY_ID . ", "
            . RdsDeliveryExecutionService::COLUMN_USER_ID . ", "
            . RdsDeliveryExecutionService::COLUMN_STATUS . ", "
            . RdsDeliveryExecutionService::COLUMN_FINISHED_AT . ", "
            . RdsDeliveryExecutionService::COLUMN_STARTED_AT . ", "
            . RdsDeliveryExecutionService::COLUMN_LABEL
            . ") VALUES ("
            . "'test', 'test', 'test', 'test', '', '"
            . $this->persistence->getPlatform()->getNowExpression() . "', 'test'"
            . ")";

        $this->persistence->exec($query);
    }

    private function createResource(): core_kernel_classes_Resource|MockObject
    {
        $resourceMock = $this->createMock(core_kernel_classes_Resource::class);
        $resourceMock->method('getUri')->willReturn('http://tao.lu/test#1');

        return $resourceMock;
    }
}
