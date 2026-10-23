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

namespace oat\taoDelivery\test\unit\model\browser\Service;

use GuzzleHttp\Psr7\ServerRequest;
use oat\tao\model\browser\Entity\Browser;
use oat\tao\model\browser\Entity\LockDownBrowserConfiguration;
use oat\tao\model\browser\Service\HttpRequestLockDownBrowserDetector;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\model\execution\DeliveryExecutionService;
use oat\taoDelivery\model\execution\OntologyDeliveryExecution;
use oat\taoDelivery\model\browser\Service\LockDownBrowserExecutionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LockDownBrowserExecutionServiceTest extends TestCase
{
    private HttpRequestLockDownBrowserDetector|MockObject $detector;

    private DeliveryExecutionService|MockObject $deliveryExecutionService;

    private LoggerInterface|MockObject $logger;

    private LockDownBrowserExecutionService $subject;

    protected function setUp(): void
    {
        $this->detector = $this->createMock(HttpRequestLockDownBrowserDetector::class);
        $this->deliveryExecutionService = $this->createMock(DeliveryExecutionService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->subject = new LockDownBrowserExecutionService(
            $this->detector,
            $this->deliveryExecutionService,
            $this->logger
        );
    }

    public function testRecordAtInitPersistsDetectedProvider(): void
    {
        $implementation = $this->createMock(OntologyDeliveryExecution::class);
        $implementation->expects($this->once())
            ->method('getLockDownBrowser')
            ->willReturn(null);
        $implementation->expects($this->once())
            ->method('setLockDownBrowser')
            ->with(LockDownBrowserConfiguration::PROVIDER_KIOSKED);

        $this->deliveryExecutionService
            ->method('getDeliveryExecution')
            ->willReturn(new DeliveryExecution($implementation));

        $request = new ServerRequest('POST', '/');
        $this->detector
            ->expects($this->once())
            ->method('detect')
            ->with($request)
            ->willReturn(new Browser(LockDownBrowserConfiguration::PROVIDER_KIOSKED));

        $this->logger->expects($this->once())->method('debug');

        $this->subject->recordAtInit('execution-id', $request);
    }

    public function testRecordAtInitDoesNotOverwriteWhenProviderAlreadySet(): void
    {
        $request = new ServerRequest('POST', '/');
        $implementation = $this->createMock(OntologyDeliveryExecution::class);
        $implementation->expects($this->once())
            ->method('getLockDownBrowser')
            ->willReturn(LockDownBrowserConfiguration::PROVIDER_KIOSKED);
        $implementation->expects($this->never())->method('setLockDownBrowser');

        $this->deliveryExecutionService
            ->expects($this->once())
            ->method('getDeliveryExecution')
            ->with('execution-id')
            ->willReturn(new DeliveryExecution($implementation));

        $this->detector
            ->expects($this->once())
            ->method('detect')
            ->with($request)
            ->willReturn(new Browser(LockDownBrowserConfiguration::PROVIDER_KIOSKED));

        $this->logger->expects($this->once())->method('debug');

        $this->subject->recordAtInit('execution-id', $request);
    }

    public function testRecordAtInitDoesNotPersistWhenDetectionIsNull(): void
    {
        $this->deliveryExecutionService
            ->expects($this->never())
            ->method('getDeliveryExecution');

        $this->detector->method('detect')->willReturn(null);
        $this->logger->expects($this->never())->method('debug');

        $this->subject->recordAtInit('execution-id', new ServerRequest('POST', '/'));
    }

    public function testRecordAtInitSkipsWhenImplementationIsUnsupported(): void
    {
        $implementation = $this->createMock(DeliveryExecutionInterface::class);

        $this->deliveryExecutionService
            ->method('getDeliveryExecution')
            ->willReturn(new DeliveryExecution($implementation));

        $this->detector
            ->method('detect')
            ->willReturn(new Browser(LockDownBrowserConfiguration::PROVIDER_MOCK));

        $this->logger->expects($this->never())->method('debug');
        $this->logger->expects($this->never())->method('error');

        $this->subject->recordAtInit('execution-id', new ServerRequest('POST', '/'));
    }
}
