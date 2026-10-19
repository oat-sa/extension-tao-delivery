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

namespace oat\taoDelivery\test\unit\model\execution;

use oat\tao\model\lockDownBrowser\Entity\LockDownBrowserConfiguration;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\model\execution\KVDeliveryExecution;
use PHPUnit\Framework\TestCase;

class DeliveryExecutionTest extends TestCase
{
    public function testGetLockDownBrowserDelegatesToAwareImplementation(): void
    {
        $implementation = $this->createMock(KVDeliveryExecution::class);
        $implementation->method('getLockDownBrowser')
            ->willReturn(LockDownBrowserConfiguration::PROVIDER_KIOSKED);

        $subject = new DeliveryExecution($implementation);

        $this->assertSame(LockDownBrowserConfiguration::PROVIDER_KIOSKED, $subject->getLockDownBrowser());
    }

    public function testGetLockDownBrowserReturnsNullWhenImplementationIsNotAware(): void
    {
        $implementation = $this->createMock(DeliveryExecutionInterface::class);
        $subject = new DeliveryExecution($implementation);

        $this->assertNull($subject->getLockDownBrowser());
    }
}
