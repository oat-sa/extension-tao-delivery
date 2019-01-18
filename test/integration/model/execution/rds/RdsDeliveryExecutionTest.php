<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoDelivery\test\integration\model\execution\rds;

use core_kernel_classes_Resource;
use oat\generis\test\TestCase;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecution;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecutionService;

class RdsDeliveryExecutionTest extends TestCase
{
    private $classUnderTest;

    protected function setUp()
    {
        $serviceMock = $this->prophesize(RdsDeliveryExecutionService::class);
        $this->classUnderTest = new RdsDeliveryExecution($serviceMock->reveal());
    }

    /**
     * @param $variableName
     * @param $validator
     * @param $value
     *
     * @dataProvider variableProvider
     */
    public function testSetters($variableName, $validator, $value)
    {
        $getterMethod = 'get' . ucfirst($variableName);
        $setterMethod = 'set' . ucfirst($variableName);

        $this->assertTrue(method_exists($this->classUnderTest, $setterMethod));

        $this->classUnderTest->$setterMethod($value);

        $this->assertTrue($validator($this->classUnderTest->$getterMethod()));
    }

    /**
     * DataProvider for the variables of class under test
     *
     * @return array
     */
    public function variableProvider()
    {
        return [
            ['delivery', function ($value) {
                return $value === null || $value instanceof core_kernel_classes_Resource;
            }, 'test'],
            ['state', function ($value) {
                return $value === null || $value instanceof core_kernel_classes_Resource;
            }, 'test'],
        ];
    }
}
