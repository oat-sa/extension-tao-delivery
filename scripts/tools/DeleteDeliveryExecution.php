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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDelivery\scripts\tools;

use oat\oatbox\extension\script\ScriptAction;
use oat\oatbox\service\ServiceNotFoundException;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDeleteRequest;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDeleteService;
use common_report_Report as Report;
use oat\taoDelivery\model\execution\ServiceProxy;
use oat\taoQtiTest\models\TestSessionService;

/**
 * Class DeleteDeliveryExecution
 *
 * This script aims at providing a tool to properly delete a delivery execution
 * by using the currently registered DeliveryExecutionDeleteService.
 *
 * @package oat\taoDelivery\scripts\tools
 */
class DeleteDeliveryExecution extends ScriptAction
{
    public function provideDescription()
    {
        return 'TAO Delivery - Delete Delivery Execution';
    }

    public function provideOptions()
    {
        return [
            'deliveryExecution' => [
                'prefix' => 'd',
                'longPrefix' => 'deliveryExecution',
                'required' => true,
                'description' => 'A comma separated list of service IDs'
            ]
        ];
    }

    public function run()
    {
        // Main report.
        $report = new \common_report_Report(
            \common_report_Report::TYPE_INFO,
            "Script ended gracefully."
        );

        try {
            /** @var DeliveryExecutionDeleteService $deleteDeliveryExecutionService */
            $deleteDeliveryExecutionService = $this->getServiceManager()->get(DeliveryExecutionDeleteService::SERVICE_ID);
            /** @var ServiceProxy $serviceProxy */
            $serviceProxy = $this->getServiceManager()->get(ServiceProxy::SERVICE_ID);
            /** @var TestSessionService $testSessionService */
            $testSessionService = $this->getServiceManager()->get(TestSessionService::SERVICE_ID);

            $deliveryExecutionIdentifier = $this->getOption('deliveryExecution');

            if ($deliveryExecution = $serviceProxy->getDeliveryExecution($deliveryExecutionIdentifier)) {

                $deleteRequest = new DeliveryExecutionDeleteRequest(
                    $deliveryExecution->getDelivery(),
                    $deliveryExecution,
                    $testSessionService->getTestSession($deliveryExecution)
                );

                try {
                    $deleteDeliveryExecutionService->execute($deleteRequest);
                    $report->add($deleteDeliveryExecutionService->getReport());
                } catch (\Exception $e) {
                    $msg = "An unexpected error occurred while deleting Delivery Execution '${deliveryExecutionIdentifier}'.";
                    $msg .= "System returned: " . $e->getMessage();

                    return new Report(Report::TYPE_ERROR, $msg);
                }
            } else {

                return new Report(Report::TYPE_ERROR, "No Delivery Execution with identifier ''.");
            }

        } catch (ServiceNotFoundException $e) {

            return new Report(Report::TYPE_ERROR, "'DeliveryExecutionDeleteService' not registered.");
        }

        return $report;
    }

    protected function provideUsage()
    {
        return [
            'prefix' => 'h',
            'longPrefix' => 'help',
            'description' => 'Prints a help statement'
        ];
    }
}