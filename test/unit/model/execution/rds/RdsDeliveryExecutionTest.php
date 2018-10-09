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
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecution;

class RdsDeliveryExecutionTest extends TestCase
{
    private $classUnderTest;

    protected function setUp()
    {
        $this->classUnderTest = new RdsDeliveryExecution();
    }

    /**
     * @param string $variableName
     * @dataProvider variableProvider
     */
    public function testGetters($variableName)
    {
        $getterMethod = "get" . ucfirst($variableName);

        $this->assertTrue(method_exists($this->classUnderTest, $getterMethod));
        $this->assertEquals(null, $this->classUnderTest->$getterMethod());
    }

    /**
     * @param $variableName
     * @param $testValue
     * @dataProvider variableProvider
     */
    public function testSetters($variableName, $testValue)
    {
        $getterMethod = "get" . ucfirst($variableName);
        $setterMethod = "set" . ucfirst($variableName);

        $this->assertTrue(method_exists($this->classUnderTest, $setterMethod));

        $this->classUnderTest->$setterMethod($testValue);

        $this->assertEquals($testValue, $this->classUnderTest->$getterMethod());
    }

    /**
     * DataProvider for the variables of class under test
     *
     * @return array
     */
    public function variableProvider()
    {
        return [
            ["identifier", "test"],
            ["label", "test"],
            ["delivery", $this->getMockResource()],
            ["state", $this->getMockResource()],
            ["userIdentifier", "test"],
            ["startTime", "test"],
            ["finishTime", "test"],
        ];
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function getMockResource()
    {
        return $this->prophesize(\core_kernel_classes_Resource::class);
    }
}