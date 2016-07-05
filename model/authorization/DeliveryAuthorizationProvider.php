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
namespace oat\taoDelivery\model\authorization;


use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\authorization\AuthorizationProvider;

/**
 * Default authorization provider using the strainer strategy...
 *
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class DeliveryAuthorizationProvider extends ConfigurableService implements AuthorizationProvider
{

    /**
     * Always authorize
     * @return boolean true always
     */
    public function isAuthorized()
    {
        return true;
    }

    /**
     * Does nothing
     * @return boolean false always
     */
    public function grant()
    {
        return false;
    }

    /**
     * Does nothing
     * @return boolean false always
     */
    public function revoke()
    {
        return false;
    }
}
