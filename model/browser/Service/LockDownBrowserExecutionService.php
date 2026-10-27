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
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoDelivery\model\browser\Service;

use oat\tao\model\browser\Service\HttpRequestLockDownBrowserDetector;
use oat\taoDelivery\model\execution\DeliveryExecutionService;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecution;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class LockDownBrowserExecutionService
{
    public function __construct(
        private readonly HttpRequestLockDownBrowserDetector $detector,
        private readonly DeliveryExecutionService $deliveryExecutionService,
        private readonly LoggerInterface $logger
    ) {
    }

    public function recordAtInit(string $deliveryExecutionId, ServerRequestInterface $request): void
    {
        try {
            $provider = $this->recordFromRequest($deliveryExecutionId, $request);

            if ($provider !== null) {
                $this->logger->debug(
                    sprintf(
                        'Lock down browser recorded as %s for delivery execution %s',
                        $provider,
                        $deliveryExecutionId
                    )
                );
            }
        } catch (Throwable $exception) {
            $this->logger->error(
                sprintf(
                    'Lock down browser init recording skipped: (%s) %s',
                    get_class($exception),
                    $exception->getMessage()
                )
            );
        }
    }

    private function recordFromRequest(string $deliveryExecutionId, ServerRequestInterface $request): ?string
    {
        // Keep the cheap detection based on the request headers to avoid querying the database for delivery execution
        $provider = $this->detector->detect($request);

        if ($provider === null) {
            return null;
        }

        $execution = $this->deliveryExecutionService->getDeliveryExecution($deliveryExecutionId);
        $existing = $execution->getLockDownBrowser();

        if ($existing) {
            return $existing;
        }

        $implementation = $execution->getImplementation();

        if ($implementation instanceof OntologyDeliveryExecution || $implementation instanceof RdsDeliveryExecution) {
            $implementation->setLockDownBrowser($provider->name);

            return $provider->name;
        }

        return null;
    }
}
