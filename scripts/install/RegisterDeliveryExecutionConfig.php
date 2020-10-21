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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoDelivery\scripts\install;

use common_Exception as Exception;
use common_report_Report as Report;
use oat\oatbox\extension\InstallAction;
use oat\taoDelivery\model\execution\DeliveryExecutionConfig;
use oat\oatbox\service\exception\InvalidServiceManagerException;

class RegisterDeliveryExecutionConfig extends InstallAction
{
    /**
     * @param $params
     *
     * @throws Exception
     * @throws InvalidServiceManagerException
     *
     * @return Report
     */
    public function __invoke($params)
    {
        $deliveryExecutionConfig = new DeliveryExecutionConfig([
            DeliveryExecutionConfig::OPTION_HIDE_HOME_BUTTON => false,
            DeliveryExecutionConfig::OPTION_HIDE_LOGOUT_BUTTON => false,
        ]);

        $this->getServiceManager()->register(DeliveryExecutionConfig::SERVICE_ID, $deliveryExecutionConfig);

        return Report::createSuccess(sprintf(
            'Service "%s" successfully configured.',
            DeliveryExecutionConfig::class
        ));
    }
}
