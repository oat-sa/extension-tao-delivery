<?php

declare(strict_types=1);

namespace oat\taoDelivery\model\lockDownBrowser\Service;

use common_exception_NotImplemented;
use oat\tao\model\lockDownBrowser\Entity\LockDownBrowser;
use oat\tao\model\lockDownBrowser\Service\LockDownBrowserDetectionService;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecution;
use oat\taoDelivery\model\execution\ServiceProxy;

/**
 * Persists combined Kiosk{ed} detection on delivery execution (INV-1263).
 */
final class LockDownBrowserExecutionService
{
    public const SERVICE_ID = 'taoDelivery/LockDownBrowserExecution';

    private LockDownBrowserDetectionService $detection;

    public function __construct(LockDownBrowserDetectionService $detection)
    {
        $this->detection = $detection;
    }

    /**
     * Called from Runner/init when the client sends clientKioskApiOk on the same request as the Kiosk UA.
     */
    /**
     * @param mixed|null $clientKioskApiOk From Runner/init POST (clientKioskApiOk)
     */
    public function recordAtInit(string $deliveryExecutionId, $clientKioskApiOk = null): string
    {
        $execution = ServiceProxy::singleton()->getDeliveryExecution($deliveryExecutionId);

        if ($execution->getLockDownBrowser() === LockDownBrowser::PROVIDER_KIOSKED) {
            return LockDownBrowser::PROVIDER_KIOSKED;
        }

        $provider = $this->detection->detect($clientKioskApiOk);

        if ($provider !== LockDownBrowser::PROVIDER_KIOSKED) {
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
