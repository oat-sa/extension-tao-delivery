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

namespace oat\taoDelivery\model\execution\metadata;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class MetadataCollection implements IteratorAggregate, JsonSerializable, Countable
{
    /** @var Metadata[] */
    private array $items = [];

    public function __construct(Metadata ...$items)
    {
        foreach ($items as $item) {
            $this->items[$item->getMetadataId()] = $item;
        }
    }

    public function addMetadata(Metadata $metadata): self
    {
        $this->items[$metadata->getMetadataId()] = $metadata;

        return $this;
    }

    /**
     * @return Metadata[]|Traversable
     */
    public function getIterator(): Traversable
    {
        yield from $this->items;
    }

    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getMetadataElement(string $metadataId): ?Metadata
    {
        return $this->items[$metadataId] ?? null;
    }
}
