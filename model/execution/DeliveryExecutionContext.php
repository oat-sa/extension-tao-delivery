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

use InvalidArgumentException;

class DeliveryExecutionContext implements DeliveryExecutionContextInterface
{
    public const EXECUTION_CONTEXT_TYPE = 'delivery';

    /**
     * @var string
     */
    private $executionId;

    /**
     * @var string
     */
    private $executionContextId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $extraData = [];

    /**
     * DeliveryExecutionContext constructor.
     *
     * @param string $executionId
     * @param string $executionContextId
     * @param string $type
     * @param string $label
     * @param array|null $extraData
     */
    public function __construct(
        string $executionId,
        string $executionContextId,
        string $type,
        string $label,
        ?array $extraData = []
    ) {
        $this->validateExecutionId($executionId);
        $this->validateExecutionContextId($executionContextId);

        $this->executionId = $executionId;
        $this->executionContextId = $executionContextId;
        $this->type = $type;
        $this->label = $label;
        $this->extraData = $extraData;
    }

    /**
     * @param array $contextData
     * @return DeliveryExecutionContextInterface
     */
    public static function createFromArray(array $contextData): DeliveryExecutionContextInterface
    {
        $executionId = $contextData[self::PARAM_EXECUTION_ID] ?? '';
        $executionContextId = $contextData[self::PARAM_CONTEXT_ID] ?? '';
        $type = $contextData[self::PARAM_TYPE] ?? '';
        $label = $contextData[self::PARAM_LABEL] ?? '';
        $extraData = $contextData[self::PARAM_EXTRA_DATA] ?? [];

        return new static($executionId, $executionContextId, $type, $label, $extraData);
    }

    /**
     * @param $executionId
     * @throws InvalidArgumentException
     */
    private function validateExecutionId($executionId)
    {
        if (empty($executionId)) {
            throw new InvalidArgumentException('Execution ID value must be not empty string.');
        }
    }

    /**
     * @param $executionContextId
     * @throws InvalidArgumentException
     */
    private function validateExecutionContextId($executionContextId)
    {
        if (empty($executionContextId)) {
            throw new InvalidArgumentException('Execution context ID value must be not empty string.');
        }
    }

    /**
     * @return string
     */
    public function getExecutionId(): string
    {
        return $this->executionId;
    }

    /**
     * @param string $executionId
     * @throws InvalidArgumentException
     */
    public function setExecutionId(string $executionId): void
    {
        $this->validateExecutionId($executionId);

        $this->executionId = $executionId;
    }

    /**
     * @param string $contextId
     * @throws InvalidArgumentException
     */
    public function setExecutionContextId(string $contextId): void
    {
        $this->validateExecutionContextId($contextId);

        $this->executionContextId = $contextId;
    }

    /**
     * @return string
     */
    public function getExecutionContextId(): string
    {
        return $this->executionContextId;
    }


    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return array
     */
    public function getExtraData(): array
    {
        return $this->extraData;
    }

    /**
     * @param array $data
     */
    public function setExtraData(array $data): void
    {
        $this->extraData = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            self::PARAM_EXECUTION_ID => $this->getExecutionId(),
            self::PARAM_CONTEXT_ID => $this->getExecutionContextId(),
            self::PARAM_TYPE => $this->getType(),
            self::PARAM_LABEL => $this->getLabel(),
            self::PARAM_EXTRA_DATA => $this->extraData,
        ];
    }
}
