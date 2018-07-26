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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoDelivery\model\container\delivery;

use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\container\execution\ExecutionServiceContainer;

class DeliveryServiceContainer extends AbstractContainer
{
    const DEFAULT_ID = 'service';

    public function getRuntime(DeliveryExecution $execution)
    {
        return \tao_models_classes_service_ServiceCall::fromJSON($this->getRuntimeParams());
    }

    public function getExecutionContainer(DeliveryExecution $execution)
    {
        $container = new ExecutionServiceContainer($execution);
        $container->setData('deliveryExecution', $execution->getIdentifier());
        $container->setData('deliveryServerConfig', []);
        $container->setData('serviceApi', \tao_helpers_ServiceJavascripts::getServiceApi(
            $this->getRuntime($execution), $execution->getIdentifier()
        ));
        return $container;
    }
}
