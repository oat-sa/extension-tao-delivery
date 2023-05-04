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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoDelivery\model\execution;

use JsonSerializable;

interface DeliveryExecutionContextInterface extends JsonSerializable
{
    public const PARAM_EXECUTION_ID = 'execution_id';
    public const PARAM_CONTEXT_ID = 'context_id';
    public const PARAM_TYPE = 'type';
    public const PARAM_LABEL = 'label';
    public const PARAM_EXTRA_DATA = 'extra_data';

    /**
     * @param string $executionId
     */
    public function setExecutionId(string $executionId): void;

    /**
     * @return string
     */
    public function getExecutionId(): string;

    /**
     * @param string $contextId
     */
    public function setExecutionContextId(string $contextId): void;

    /**
     * @return string
     */
    public function getExecutionContextId(): string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $label
     */
    public function setLabel(string $label): void;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @param array $data
     */
    public function setExtraData(array $data): void;

    /**
     * @return array
     */
    public function getExtraData(): array;
}
