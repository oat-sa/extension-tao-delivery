<?php

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