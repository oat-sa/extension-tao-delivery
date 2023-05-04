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

use Exception;
use http\Exception\BadQueryStringException;
use oat\taoDelivery\model\execution\DeliveryExecutionService;
use tao_actions_RestController;

class DeliveryExecution extends tao_actions_RestController
{
    public const ATTRIBUTE_DELIVERY_EXECUTION_URI = 'execution';

    public function get(DeliveryExecutionService $deliveryExecutionService): void
    {
        try {
            $queryParams = $this->getPsrRequest()->getQueryParams();
            $this->validateRequestAttributes($queryParams);

            $de = $deliveryExecutionService->getDeliveryExecution(
                $queryParams[self::ATTRIBUTE_DELIVERY_EXECUTION_URI]
            );

            $responsePayload = $de->jsonSerialize();
        } catch (Exception $exception) {
            $this->returnFailure($exception);
        }

        $this->returnSuccess($responsePayload);
    }

    private function validateRequestAttributes(array $queryParams): void
    {
        if (!isset($queryParams[self::ATTRIBUTE_DELIVERY_EXECUTION_URI])) {
            throw new BadQueryStringException(
                sprintf('Missing %s query', self::ATTRIBUTE_DELIVERY_EXECUTION_URI)
            );
        };
    }
}
