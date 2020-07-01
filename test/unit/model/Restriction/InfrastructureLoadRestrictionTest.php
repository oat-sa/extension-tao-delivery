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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

namespace oat\taoDelivery\test\unit\model\Restriction;

use oat\generis\test\TestCase;
use oat\tao\model\metrics\implementations\abstractMetrics;
use oat\tao\model\metrics\MetricsService;
use oat\taoDelivery\model\Capacity\CapacityInterface;
use oat\taoDelivery\model\Restriction\CapacityRestriction;
use oat\taoDelivery\model\Restriction\InfrastructureLoadRestriction;
use PHPUnit\Framework\MockObject\MockObject;

class InfrastructureLoadRestrictionTest extends TestCase
{
    /**
     * @var CapacityRestriction
     */
    private $subject;

    /**
     * @var MetricsService|MockObject
     */
    private $metricsServiceMock;

    public function setUp(): void
    {
        $this->metricsServiceMock = $this->createMock(MetricsService::class);

        $this->subject = new InfrastructureLoadRestriction();
        $this->subject->setServiceLocator($this->getServiceLocatorMock([
            MetricsService::class => $this->metricsServiceMock,
        ]));
        parent::setUp();
    }

    public function testDoesComply_WhenConfigValueIsZero_ThenRestrictionIsNotApplied()
    {
        $this->metricsServiceMock->expects($this->never())->method('getOneMetric');
        $this->assertTrue($this->subject->doesComply(0));
    }

    public function testDoesComply_WhenConfigValueIsProvided_ThenItIsComparedWithLoadMetric()
    {
        $metricsMock = $this->createMock(abstractMetrics::class);
        $metricsMock->method('collect')->willReturn(10);

        $this->metricsServiceMock->method('getOneMetric')->willReturn($metricsMock);
        $this->assertTrue($this->subject->doesComply(11));
        $this->assertFalse($this->subject->doesComply(10));
        $this->assertFalse($this->subject->doesComply(9));
    }
}
