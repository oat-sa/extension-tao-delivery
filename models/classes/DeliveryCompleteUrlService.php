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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 * @author Oleksander Zagovorychev <olexander.zagovorychev@1pt.com>
 */
namespace oat\taoDelivery\models\classes;

use oat\oatbox\service\ConfigurableService;

class DeliveryCompleteUrlService extends ConfigurableService
{

    const SERVICE_ID = 'taoDelivery/deliveryCompleteUrl';

    const EXTENSION_OPTION = 'extension';
    const CONTROLLER_OPTION = 'controller';
    const METHOD_OPTION = 'method';

    /**
     * Get the full url to go at the end of a test
     * @return string the full url
     */
    public function getUrl()
    {
        $ext = ($this->hasOption(self::EXTENSION_OPTION)) ? $this->getOption(self::EXTENSION_OPTION) : 'taoDelivery';
        $ctrl = ($this->hasOption(self::CONTROLLER_OPTION)) ? $this->getOption(self::CONTROLLER_OPTION) : 'DeliveryServer';
        $method = ($this->hasOption(self::METHOD_OPTION)) ? $this->getOption(self::METHOD_OPTION) : 'index';
        return _url($method, $ctrl, $ext);
    }
}