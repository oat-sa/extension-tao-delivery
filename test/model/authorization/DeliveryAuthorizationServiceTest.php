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
namespace oat\taoProctoring\test\model\authorization;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\User;
use oat\taoDelivery\model\authorization\AuthorizationProvider;
use oat\taoDelivery\model\authorization\AuthorizationService;
use oat\taoDelivery\model\authorization\DeliveryAuthorizationProvider;
use oat\taoDelivery\model\authorization\DeliveryAuthorizationService;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * Test the DeliveryAuthorizationService
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class DeliveryAuthorizationServiceTest extends TaoPhpUnitTestRunner
{


    /**
     * Test the DeliveryAuthorizationService
     */
    public function testGetAuthorizationServiceAPI()
    {
        $authorizationService = new DeliveryAuthorizationService();
        $this->assertInstanceOf(AuthorizationService::class, $authorizationService);
        $this->assertInstanceOf(ConfigurableService::class, $authorizationService);
    }

    /**
     * Create a dummy variable for a DeliveryExecution
     * @return DeliveryExecution the dummy variable
     */
    protected function getDeliveryExecution()
    {
        $prophet = new \Prophecy\Prophet();
        $prophecy = $prophet->prophesize();
        $prophecy->willImplement(DeliveryExecution::class);

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
     * Test getting the authoriation provider
     */
    public function testGetAuthorizationProvider()
    {
        $authorizationService = new DeliveryAuthorizationService();
        $provider = $authorizationService->getAuthorizationProvider($this->getDeliveryExecution(), $this->getUser());

        $this->assertInstanceOf(AuthorizationProvider::class, $provider);
        $this->assertInstanceOf(DeliveryAuthorizationProvider::class, $provider);
    }

}
