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

namespace oat\taoDelivery\test\unit\model\execution\metadata;

use oat\taoDelivery\model\execution\metadata\Metadata;
use oat\taoDelivery\model\execution\metadata\MetadataCollection;
use PHPUnit\Framework\TestCase;

class MetadataCollectionTest extends TestCase
{
    private Metadata $metadata;

    public function testAddMetadata(): void
    {
        $metadata = new Metadata('someId', 'someContent');
        $subject = new MetadataCollection();
        $result = $subject->addMetadata($metadata);
        self::assertInstanceOf(MetadataCollection::class, $result);
        self::assertEquals(1, $subject->count());
        self::assertEquals(
            [
                'someId' => new Metadata('someId', 'someContent')
            ],
            $subject->jsonSerialize()
        );
    }
}
