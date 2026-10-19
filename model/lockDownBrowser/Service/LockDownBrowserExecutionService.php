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

namespace oat\taoDelivery\model\lockDownBrowser\Service;

use common_exception_NotImplemented;
use oat\tao\model\lockDownBrowser\Service\LockDownBrowserDetectorInterface;
use oat\taoDelivery\model\execution\DeliveryExecutionService;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecution;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class LockDownBrowserExecutionService
{
    public function __construct(
        private LockDownBrowserDetectorInterface $detector,
        private DeliveryExecutionService $deliveryExecutionService,
        private LoggerInterface $logger
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
        $execution = $this->deliveryExecutionService->getDeliveryExecution($deliveryExecutionId);
        $existing = $execution->getLockDownBrowser();

        if ($existing) {
            return $existing;
        }

        $provider = $this->detector->detect($request);

        if ($provider === null) {
            return null;
        }

        $implementation = $execution->getImplementation();

        if ($implementation instanceof OntologyDeliveryExecution || $implementation instanceof RdsDeliveryExecution) {
            $implementation->setLockDownBrowser($provider);

            return $provider;
        }

        throw new common_exception_NotImplemented(
            sprintf('Lock down browser recording is not implemented for %s', get_class($implementation))
        );
    }
}
