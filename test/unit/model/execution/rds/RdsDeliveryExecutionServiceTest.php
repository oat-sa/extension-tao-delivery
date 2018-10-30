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
namespace oat\taoDelivery\test\unit\model\execution\rds;

use oat\generis\test\TestCase;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDeleteRequest;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecutionService;
use oat\taoDelivery\scripts\install\GenerateRdsDeliveryExecutionTable;

class RdsDeliveryExecutionServiceTest extends TestCase
{
    /** @var RdsDeliveryExecutionService */
    private $classUnderTest;

    /** @var \common_persistence_Persistence */
    private $persistence;

    public function setUp()
    {
        $managerMock = $this->getSqlMock("default");
        $serviceLocatorMock = $this->getServiceLocatorMock([
            \common_persistence_Manager::SERVICE_ID => $managerMock,
        ]);

        $this->persistence = $managerMock->getPersistenceById("default");

        $rdsHelper = new GenerateRdsDeliveryExecutionTable();
        $rdsHelper->generateTable($this->persistence);

        $this->classUnderTest = $this
            ->getMockBuilder(RdsDeliveryExecutionService::class)
            ->setMethods(["getNewUri"])
            ->getMock()
        ;

        $this->classUnderTest->setServiceLocator($serviceLocatorMock);
        $this->classUnderTest->method("getNewUri")->willReturn("test");
    }

    public function tearDown()
    {
        $this->classUnderTest = null;
    }

    public function testClassMethods()
    {
        $this->assertTrue(method_exists($this->classUnderTest, "deleteDeliveryExecutionData"));
        $this->assertTrue(method_exists($this->classUnderTest, "getExecutionsByDelivery"));
        $this->assertTrue(method_exists($this->classUnderTest, "getUserExecutions"));
        $this->assertTrue(method_exists($this->classUnderTest, "getDeliveryExecutionsByStatus"));
        $this->assertTrue(method_exists($this->classUnderTest, "spawnDeliveryExecution"));
        $this->assertTrue(method_exists($this->classUnderTest, "initDeliveryExecution"));
        $this->assertTrue(method_exists($this->classUnderTest, "getDeliveryExecution"));
    }

    public function testClassContants()
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

    public function testDeleteDeliveryExecutionData()
    {
        $deliveryExecutionMock = $this->prophesize(DeliveryExecution::class);
        $deliveryExecutionMock->getIdentifier()->willReturn("test");

        $requestMock = $this->prophesize(DeliveryExecutionDeleteRequest::class);
        $requestMock->getDeliveryExecution()->willReturn($deliveryExecutionMock);

        $result = $this->classUnderTest->deleteDeliveryExecutionData($requestMock->reveal());

        $this->assertTrue(is_integer($result));
        $this->assertTrue($result === 0);
    }

    public function testDeleteDeliveryExecutionDataWithNonEmptyDatabase()
    {
        $this->insertNewRow();

        $deliveryExecutionMock = $this->prophesize(DeliveryExecution::class);
        $deliveryExecutionMock->getIdentifier()->willReturn("test");

        $requestMock = $this->prophesize(DeliveryExecutionDeleteRequest::class);
        $requestMock->getDeliveryExecution()->willReturn($deliveryExecutionMock);

        $result = $this->classUnderTest->deleteDeliveryExecutionData($requestMock->reveal());

        $this->assertTrue(is_integer($result));
        $this->assertTrue($result === 1);
    }

    public function testGetExecutionsByDeliveryWithEmptyDatabase()
    {
        $resourceMock = $this->prophesize(\core_kernel_classes_Resource::class);

        $this->assertTrue(is_array($this->classUnderTest->getExecutionsByDelivery($resourceMock->reveal())));
        $this->assertTrue(count($this->classUnderTest->getExecutionsByDelivery($resourceMock->reveal())) === 0);
    }

    public function testGetExecutionsByDeliveryWithNonEmptyDatabase()
    {
        $this->insertNewRow();

        $resourceMock = $this->prophesize(\core_kernel_classes_Resource::class);

        $resourceMock->getUri()->willReturn("test");

        $this->assertTrue(is_array($this->classUnderTest->getExecutionsByDelivery($resourceMock->reveal())));
        $this->assertTrue(count($this->classUnderTest->getExecutionsByDelivery($resourceMock->reveal())) === 1);
    }

    public function testGetUserExecutions()
    {
        $resourceMock = $this->prophesize(\core_kernel_classes_Resource::class);

        $this->assertTrue(is_array($this->classUnderTest->getUserExecutions($resourceMock->reveal(), "test")));
        $this->assertTrue(count($this->classUnderTest->getExecutionsByDelivery($resourceMock->reveal())) === 0);
    }

    public function testGetUserExecutionsWithNonEmptyDatabase()
    {
        $this->insertNewRow();

        $resourceMock = $this->prophesize(\core_kernel_classes_Resource::class);

        $resourceMock->getUri()->willReturn("test");

        $this->assertTrue(is_array($this->classUnderTest->getUserExecutions($resourceMock->reveal(), "test")));
        $this->assertTrue(count($this->classUnderTest->getUserExecutions($resourceMock->reveal(), "test")) === 1);
    }

    public function testGetDeliveryExecutionsByStatus()
    {
        $this->assertTrue(is_array($this->classUnderTest->getDeliveryExecutionsByStatus("test", "test")));
        $this->assertTrue(count($this->classUnderTest->getDeliveryExecutionsByStatus("test", "test")) === 0);
    }

    public function testGetDeliveryExecutionsByStatusWithNonEmptyDatabase()
    {
        $this->insertNewRow();

        $this->assertTrue(is_array($this->classUnderTest->getDeliveryExecutionsByStatus("test", "test")));
        $this->assertTrue(count($this->classUnderTest->getDeliveryExecutionsByStatus("test", "test")) === 1);
    }

    public function testSpawnDeliveryExecution()
    {
        $this->assertInstanceOf(DeliveryExecution::class, $this->classUnderTest->spawnDeliveryExecution("test", "test", "test", "test"));
    }

    public function testInitDeliveryExecution()
    {
        $resourceMock = $this->prophesize(\core_kernel_classes_Resource::class);

        $resourceMock->getLabel()->willReturn("test");
        $resourceMock->getUri()->willReturn("test");

        $this->assertInstanceOf(DeliveryExecution::class, $this->classUnderTest->initDeliveryExecution($resourceMock->reveal(), "test"));
    }

    public function testGetDeliveryExecution()
    {
        $this->assertInstanceOf(DeliveryExecution::class, $this->classUnderTest->getDeliveryExecution("test"));
    }

    public function testGetPersistence()
    {
        $this->assertInstanceOf(\common_persistence_SqlPersistence::class, $this->classUnderTest->getPersistence());
    }

    private function insertNewRow()
    {
        $currentDateTime = new \DateTime();

        $query = "INSERT INTO " . RdsDeliveryExecutionService::TABLE_NAME . " ("
            . RdsDeliveryExecutionService::COLUMN_ID . ", "
            . RdsDeliveryExecutionService::COLUMN_DELIVERY_ID . ", "
            . RdsDeliveryExecutionService::COLUMN_USER_ID . ", "
            . RdsDeliveryExecutionService::COLUMN_STATUS . ", "
            . RdsDeliveryExecutionService::COLUMN_FINISHED_AT . ", "
            . RdsDeliveryExecutionService::COLUMN_STARTED_AT . ", "
            . RdsDeliveryExecutionService::COLUMN_LABEL
            . ") VALUES ("
            . "'test', 'test', 'test', 'test', '', '" .     $currentDateTime->format("Y-m-d H:i:s") . "', 'test'"
            . ")"
        ;

        $this->persistence->exec($query);

    }
}