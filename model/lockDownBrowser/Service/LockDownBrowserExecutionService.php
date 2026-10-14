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

use common_Logger;
use common_exception_NotImplemented;
use oat\tao\model\lockDownBrowser\Entity\LockDownBrowser;
use oat\tao\model\lockDownBrowser\Service\LockDownBrowserDetectorInterface;
use oat\taoDelivery\model\execution\DeliveryExecutionService;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecution;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Persists combined Kiosk{ed} detection on delivery execution (INV-1263).
 */
final class LockDownBrowserExecutionService
{
    private LockDownBrowserDetectorInterface $detector;

    private DeliveryExecutionService $deliveryExecutionService;

    public function __construct(
        LockDownBrowserDetectorInterface $detector,
        DeliveryExecutionService $deliveryExecutionService
    ) {
        $this->detector = $detector;
        $this->deliveryExecutionService = $deliveryExecutionService;
    }

    public function recordAtInit(string $deliveryExecutionId, ServerRequestInterface $request): string
    {
        try {
            $provider = $this->recordFromRequest($deliveryExecutionId, $request);

            if ($provider === LockDownBrowser::PROVIDER_KIOSKED
                || $provider === LockDownBrowser::PROVIDER_KIOSKED_MOCK
            ) {
                common_Logger::i(
                    'Lock down browser recorded as ' . $provider . ' for delivery execution '
                    . $deliveryExecutionId
                );
            }

            return $provider;
        } catch (\Throwable $exception) {
            common_Logger::w(
                'Lock down browser init recording skipped: ' . $exception->getMessage()
            );

            return LockDownBrowser::PROVIDER_NONE;
        }
    }

    private function recordFromRequest(string $deliveryExecutionId, ServerRequestInterface $request): string
    {
        $execution = $this->deliveryExecutionService->getDeliveryExecution($deliveryExecutionId);

        $existing = $execution->getLockDownBrowser();

        if ($existing === LockDownBrowser::PROVIDER_KIOSKED
            || $existing === LockDownBrowser::PROVIDER_KIOSKED_MOCK
        ) {
            return $existing;
        }

        $provider = $this->detector->detect($request);

        if ($provider !== LockDownBrowser::PROVIDER_KIOSKED
            && $provider !== LockDownBrowser::PROVIDER_KIOSKED_MOCK
        ) {
            return LockDownBrowser::PROVIDER_NONE;
        }

        $this->applyProvider($execution->getImplementation(), $provider);

        return $provider;
    }

    /**
     * @param OntologyDeliveryExecution|RdsDeliveryExecution $implementation
     */
    private function applyProvider($implementation, string $provider): void
    {
        if ($implementation instanceof OntologyDeliveryExecution) {
            $implementation->setLockDownBrowser($provider);

            return;
        }

        if ($implementation instanceof RdsDeliveryExecution) {
            $implementation->setLockDownBrowser($provider);
            // ponytail: in-memory only until delivery execution is flushed by existing RDS lifecycle

            return;
        }

        throw new common_exception_NotImplemented(
            'Lock down browser recording is only implemented for ontology and RDS executions'
        );
    }
}
