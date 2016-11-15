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
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */

namespace oat\taoDelivery\scripts\cli;

use oat\oatbox\action\Action;
use oat\taoDelivery\model\execution\DeliveryExecution;

class DeliveryExecutionStatus implements Action
{
    public function __invoke($params)
    {
        $deliveryExecutionId = trim($params[0]);
        switch (strtolower($params[1])) {
            case 'active':
                $deliveryExecutionState = DeliveryExecution::STATE_ACTIVE;
                break;
                
            case 'paused':
                $deliveryExecutionState = DeliveryExecution::STATE_PAUSED;
                break;
                
            case 'finished':
                $deliveryExecutionState = DeliveryExecution::STATE_FINISHIED;
                break;
                
            default:
                return \common_report_Report::createFailure('Unknown state given.');
                break;
        }
        
        $service = \taoDelivery_models_classes_execution_ServiceProxy::singleton();
        $deliveryExecution = $service->getDeliveryExecution($deliveryExecutionId);
        $deliveryExecution->setState($deliveryExecutionState);
        
        return \common_report_Report::createSuccess("Delivery Status of execution '${deliveryExecutionId}' has been changed.");
    }
}
