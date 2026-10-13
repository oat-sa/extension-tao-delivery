<?php

declare(strict_types=1);

namespace oat\taoDelivery\model\lockDownBrowser\ServiceProvider;

use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\tao\model\lockDownBrowser\Service\LockDownBrowserDetectionService;
use oat\taoDelivery\model\lockDownBrowser\Service\LockDownBrowserExecutionService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * @codeCoverageIgnore
 */
class LockDownBrowserServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services
            ->set(LockDownBrowserExecutionService::class, LockDownBrowserExecutionService::class)
            ->public()
            ->args([service(LockDownBrowserDetectionService::class)]);

        $services
            ->alias(LockDownBrowserExecutionService::SERVICE_ID, LockDownBrowserExecutionService::class)
            ->public();
    }
}
