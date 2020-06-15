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

namespace oat\taoDelivery\test\unit\model\Capacity;

use oat\generis\test\TestCase;
use oat\taoDelivery\model\Capacity\CapacityInterface;
use oat\taoDelivery\model\Capacity\CapacityRestriction;
use PHPUnit\Framework\MockObject\MockObject;

class CapacityRestrictionTest extends TestCase
{
    /**
     * @var CapacityInterface|MockObject
     */
    private $capacityMock;
    /**
     * @var CapacityRestriction
     */
    private $subject;

    public function setUp(): void
    {
        $this->capacityMock = $this->createMock(CapacityInterface::class);
        $this->subject = new CapacityRestriction();
        $this->subject->setServiceLocator($this->getServiceLocatorMock([
            CapacityInterface::SERVICE_ID => $this->capacityMock,
        ]));
        parent::setUp();
    }

    public function testDoesComplies_WhenConfigValueIsZero_ThenRestrictionIsNotApplied()
    {
        $this->capacityMock->expects($this->never())->method('consume');
        $this->assertTrue($this->subject->doesComplies(0));
    }

    public function testDoesComplies_WhenConfigValueIsProvided_ThenCapacityIsConsumed()
    {
        $this->capacityMock->expects($this->once())->method('consume')->willReturn(true);
        $this->assertTrue($this->subject->doesComplies([]));
    }
}
