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
 * Copyright (c) 2017  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoDelivery\test\model\delivery;


use oat\taoDelivery\model\delivery\Delivery;
use oat\taoDelivery\model\delivery\DeliveryInterface;
use oat\taoDelivery\test\model\delivery\sample\DeliveryServiceSample;

class DeliveryServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DeliveryServiceSample
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = new DeliveryServiceSample();
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage Delivery not found [isNotDelivery]
     */
    public function testGetParameterWithNoDelivery()
    {
        $this->service->getParameter('isNotDelivery', 'parameterName');
    }

    public function testGetParameterWithNoParameter()
    {
        $this->assertNull($this->service->getParameter('delivery1', 'parameterName'), 'Parameter does not exist');
    }

    public function testGetParameter()
    {
        $this->assertEquals('valueOfParameter1', $this->service->getParameter('delivery1', 'param1'), 'Parameter exists');
    }

    public function testCreateDelivery()
    {
        $deliveryClass = new \core_kernel_classes_Class(DeliveryInterface::ASSEMBLED_DELIVERY);
        $delivery = $this->service->createDelivery($deliveryClass, 'testDelivery');
        self::assertInstanceOf(Delivery::class, $delivery, 'Delivery returned');
        self::assertEquals('testDelivery', $delivery->getLabel());
        self::assertEquals($this->service->getLabel($delivery->getIdentifier()), $delivery->getLabel());
    }

    public function testParameters()
    {
        $delivery = new Delivery('delivery1', $this->service);
        self::assertEquals('delivery1', $delivery->getIdentifier(), 'Identifier is correct');
        self::assertNull($delivery->getLabel(), 'Label not set');
        $delivery->setLabel('label-val');
        self::assertEquals('label-val', $delivery->getLabel(), 'Label correct');
        $delivery->setResultServer('rs');
        self::assertEquals('rs', $delivery->getResultServer(), 'Result Server was installed');
        $excluded = ['ex1', 'ex2'];
        $delivery->setExcludedSubjects($excluded);
        self::assertEquals($excluded, $delivery->getExcludedSubjects());
        $delivery->setCompilationRuntime('cr');
        self::assertEquals('cr', $delivery->getCompilationRuntime());
        $time = time();
        $delivery->setCompilationDate($time);
        self::assertEquals($time, $delivery->getCompilationDate());
        $delivery->setMaxExec(4);
        self::assertEquals(4, $delivery->getMaxExec());
        $delivery->setParameters([
            DeliveryInterface::MAX_EXEC => 5,
            DeliveryInterface::ASSEMBLED_DELIVERY_TIME => $time + 5,
            DeliveryInterface::ASSEMBLED_DELIVERY_RUNTIME => 'assembled_runtime',
            DeliveryInterface::EXCLUDED_SUBJECTS => ['ex2', 'ex5'],
        ]);
        self::assertEquals(5, $delivery->getMaxExec());
        self::assertEquals($time + 5, $delivery->getCompilationDate());
        self::assertEquals('assembled_runtime', $delivery->getCompilationRuntime());
        self::assertEquals(['ex2', 'ex5'], $delivery->getExcludedSubjects());
    }
}
