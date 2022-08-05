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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoDelivery\controller;

use common_exception_NoImplementation;
use Exception;
use oat\taoDelivery\model\execution\DeliveryExecutionMetadataInterface;
use oat\taoDelivery\model\execution\DeliveryExecutionService;
use oat\taoDelivery\model\execution\implementation\KeyValueService;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use \tao_actions_RestController;

class DeliveryExecution extends tao_actions_RestController
{
    public function get(DeliveryExecutionService $deliveryExecutionService): void
    {
        if (!($deliveryExecutionService instanceof KeyValueService)) {
            $this->returnFailure(new common_exception_NoImplementation());
        }

        try {
            $de = $this->getDeliveryExecution($deliveryExecutionService);
        } catch (Exception $exception) {
            $this->returnFailure($exception);
        }

        $this->returnSuccess([
            OntologyDeliveryExecution::PROPERTY_DELIVERY => $de->getDelivery()->getUri(),
            OntologyDeliveryExecution::PROPERTY_SUBJECT => $de->getUserIdentifier(),
            OntologyDeliveryExecution::PROPERTY_TIME_START => $de->getStartTime(),
            OntologyDeliveryExecution::PROPERTY_TIME_END => $de->getFinishTime(),
            OntologyDeliveryExecution::PROPERTY_STATUS => $de->getState()->getLabel(),
            DeliveryExecutionMetadataInterface::PROPERTY_METADATA => $de->getAllMetadata(),
        ]);
    }

    private function getDeliveryExecution(DeliveryExecutionService $deliveryExecutionService): DeliveryExecutionInterface
    {
        $queryBag = $this->getPsrRequest()->getQueryParams();
        return  $deliveryExecutionService
            ->getDeliveryExecution(KeyValueService::DELIVERY_EXECUTION_PREFIX . $queryBag['execution'])
            ->getImplementation();
    }
}
