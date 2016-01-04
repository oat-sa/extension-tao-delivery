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

namespace oat\taoDelivery\adapter;

use oat\oatbox\service\ServiceManager;
use oat\taoDelivery\model\AssignmentService;
use oat\taoDelivery\model\TestRunnerAdapterInterface;

class TestRunnerLegacyAdapter implements TestRunnerAdapterInterface
{
    /**
     * @inheritDoc
     */
    public function run(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution)
    {
        $delivery = $deliveryExecution->getDelivery();
        $runtime = ServiceManager::getServiceManager()->get(AssignmentService::CONFIG_ID)->getRuntime($delivery);

        return [
            'serviceApi' => \tao_helpers_ServiceJavascripts::getServiceApi($runtime, $deliveryExecution->getIdentifier()),
            'content-adapter' => 'DeliveryServer/adapter/legacy/adapter.tpl',
            'content-template' => 'DeliveryServer/adapter/legacy/template.tpl',
        ];
    }
}
