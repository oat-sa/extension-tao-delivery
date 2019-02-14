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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\test\unit\model\authorization;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoDelivery\model\authorization\strategy\StateValidation;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * Test the StateValidation
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class StateValidationTest extends TaoPhpUnitTestRunner
{
    /**
     * Create a dummy variable for a DeliveryExecution
     * @return DeliveryExecution the dummy variable
     */
    protected function getDeliveryExecution($state)
    {
        $prophet = new \Prophecy\Prophet();
        $prophecyState = $prophet->prophesize(\core_kernel_classes_Resource::class);
        $prophecyState->getUri()->willReturn($state);
        
        $prophet = new \Prophecy\Prophet();
        $prophecy = $prophet->prophesize();
        $prophecy->willImplement(DeliveryExecutionInterface::class);
        $prophecy->getState()->willReturn($prophecyState->reveal());
        return $prophecy->reveal();
    }
    /**
     * Create a dummy variable for a User
     * @return User the dummy variable
     */
    protected function getUser()
    {
        $prophet = new \Prophecy\Prophet();
        $prophecy = $prophet->prophesize();
        $prophecy->willImplement(User::class);
        return $prophecy->reveal();
    }

    /**
     * Test the DeliveryAuthorizationProvider#isAuthorized method
     */
    public function testNotAuthorized()
    {
        $this->setExpectedException(\common_exception_Unauthorized::class);
        $validator = new StateValidation();
        $validator->verifyResumeAuthorization($this->getDeliveryExecution(DeliveryExecution::STATE_FINISHED), $this->getUser());
    }
}
