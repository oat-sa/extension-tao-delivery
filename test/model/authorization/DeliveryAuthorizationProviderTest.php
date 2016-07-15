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
namespace oat\taoDelivery\test\model\authorization;

use oat\taoDelivery\model\authorization\AuthorizationProvider;
use oat\taoDelivery\model\authorization\DeliveryAuthorizationProvider;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * Test the DeliveryAuthorizationProvider
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class DeliveryAuthorizationProviderTest extends TaoPhpUnitTestRunner
{

    /**
     * Test the DeliveryAuthorizationProvider API
     */
    public function testGetAuthorizationProviderAPI()
    {
        $authorizationProvider = new DeliveryAuthorizationProvider();
        $this->assertInstanceOf(AuthorizationProvider::class, $authorizationProvider, "Check if the provider implements the authorizationProvider interface");
    }

    /**
     * Test the DeliveryAuthorizationProvider#isAuthorized method
     */
    public function testIsAuthorized()
    {
        $authorizationProvider = new DeliveryAuthorizationProvider();
        $this->assertTrue($authorizationProvider->isAuthorized(), "This implementation always authorize");
    }

    /**
     * Test the DeliveryAuthorizationProvider#grant method
     */
    public function testGrant()
    {
        $authorizationProvider = new DeliveryAuthorizationProvider();
        $this->assertFalse($authorizationProvider->grant(), "This implementation does nothing, it's always false");
    }

    /**
     * Test the DeliveryAuthorizationProvider#revoke method
     */
    public function testRevoke()
    {
        $authorizationProvider = new DeliveryAuthorizationProvider();
        $this->assertFalse($authorizationProvider->revoke(), "This implementation does nothing, it's always false");
    }
}
