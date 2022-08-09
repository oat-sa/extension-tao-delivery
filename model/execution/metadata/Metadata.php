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

use JsonSerializable;

class Metadata implements JsonSerializable
{
    public const METADATA_ID = 'metadataId';
    public const METADATA_CONTENT = 'metadataContent';

    private string $metadataId;

    private $metadataContent;

    public function __construct(string $metadataId, $metadataContent)
    {
        $this->metadataId = $metadataId;
        $this->metadataContent = $metadataContent;
    }

    public function getMetadataId(): string
    {
        return $this->metadataId;
    }

    public function getMetadataContent()
    {
        return $this->metadataContent;
    }

    public function jsonSerialize(): array
    {
        return [
            self::METADATA_ID => $this->metadataId,
            self::METADATA_CONTENT => $this->metadataContent
        ];
    }
}
