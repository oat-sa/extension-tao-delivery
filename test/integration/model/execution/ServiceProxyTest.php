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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\test\integration\model\execution;

require_once dirname(__FILE__) .'/../../../../../tao/includes/raw_start.php';

use common_exception_NoImplementation;
use oat\tao\test\TaoPhpUnitTestRunner;
use common_ext_ExtensionsManager;
use oat\taoDelivery\model\execution\ServiceProxy;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\oatbox\user\User;

class ServiceProxyTest extends TaoPhpUnitTestRunner
{
    private $config;

    /**
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $this->config = $ext->getConfig(ServiceProxy::CONFIG_KEY);
    }
    /**
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $ext->setConfig(ServiceProxy::CONFIG_KEY,$this->config);

    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSetImplementation()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');
        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $new = $ext->getConfig(ServiceProxy::CONFIG_KEY);
        $this->assertEquals($service, $new);

    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetUserExecutions()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $res = $resource->reveal();
        $serviceProphecy->getUserExecutions($res, '#UserUri')->willReturn(true);

        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $return = ServiceProxy::singleton()->getUserExecutions($res, '#UserUri');

        $this->assertTrue($return);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetDeliveryExecutionsByStatus()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');
        $serviceProphecy->getDeliveryExecutionsByStatus('#UserUri','status')->willReturn(true);
        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $return = ServiceProxy::singleton()->getDeliveryExecutionsByStatus('#UserUri','status');
        $this->assertTrue($return);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetActiveDeliveryExecutions()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');
        $serviceProphecy->getDeliveryExecutionsByStatus('#UserUri', DeliveryExecutionInterface::STATE_ACTIVE)->willReturn(true);
        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $return = ServiceProxy::singleton()->getActiveDeliveryExecutions('#UserUri');
        $this->assertTrue($return);
    }
    /**
     *
     * @author Aleh Hutnikau, hutnikau@1pt.com
     */
    public function testGetPausedDeliveryExecutions()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');
        $serviceProphecy->getDeliveryExecutionsByStatus('#UserUri', DeliveryExecutionInterface::STATE_PAUSED)->willReturn(true);
        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $return = ServiceProxy::singleton()->getPausedDeliveryExecutions('#UserUri');
        $this->assertTrue($return);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetFinishedDeliveryExecutions()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');
        $serviceProphecy->getDeliveryExecutionsByStatus('#UserUri',DeliveryExecutionInterface::STATE_FINISHED)->willReturn(true);

        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $return = ServiceProxy::singleton()->getFinishedDeliveryExecutions('#UserUri');
        $this->assertTrue($return);

    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testInitDeliveryExecution()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');

        $deProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\DeliveryExecution');
        $deliveryExecution = $deProphecy->reveal();

        $deliveryProphecy = $this->prophesize('core_kernel_classes_Resource');
        $delivery = $deliveryProphecy->reveal();
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->getIdentifier()->willReturn('#UserUri');
        $user = $userProphecy->reveal();

        $serviceProphecy->initDeliveryExecution($delivery,'#UserUri')->willReturn($deliveryExecution);

        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $return = ServiceProxy::singleton()->initDeliveryExecution($delivery, $user);
        $this->assertEquals($deliveryExecution, $return);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetExecution()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');
        $serviceProphecy->getDeliveryExecution('#id')->willReturn(true);
        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $return =  ServiceProxy::singleton()->getDeliveryExecution('#id');
        $this->assertTrue($return);
    }
    /**
     * @expectedException common_exception_NoImplementation
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetExecutionsByDeliveryException()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Service');
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $res = $resource->reveal();
        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        ServiceProxy::singleton()->getExecutionsByDelivery($res);
    }
    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetExecutionsByDelivery()
    {
        $serviceProphecy = $this->prophesize('oat\\taoDelivery\\model\\execution\\Monitoring');
        $service = $serviceProphecy->reveal();
        ServiceProxy::singleton()->setImplementation($service);

        $resource = $this->prophesize('core_kernel_classes_Resource');
        $res = $resource->reveal();

        ServiceProxy::singleton()->getExecutionsByDelivery($res);
        $serviceProphecy->getExecutionsByDelivery($res)->shouldHaveBeenCalled();

    }


}
