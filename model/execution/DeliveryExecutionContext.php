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
    const EXECUTION_CONTEXT_TYPE = 'delivery';

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
     * DeliveryExecutionContext constructor.
     *
     * @param string $executionId
     * @param string $executionContextId
     * @param string $type
     * @param string $label
     */
    public function __construct($executionId, $executionContextId, $type, $label)
    {
        $this->validateExecutionId($executionId);
        $this->validateExecutionContextId($executionContextId);

        $this->executionId = $executionId;
        $this->executionContextId = $executionContextId;
        $this->type = $type;
        $this->label = $label;
    }

    /**
     * @param array $contextData
     * @return DeliveryExecutionContextInterface
     */
    public static function createFromArray(array $contextData)
    {
        $executionId = isset($contextData[self::PARAM_EXECUTION_ID]) ? $contextData[self::PARAM_EXECUTION_ID] : '';
        $executionContextId = isset($contextData[self::PARAM_CONTEXT_ID]) ? $contextData[self::PARAM_CONTEXT_ID] : '';
        $type = isset($contextData[self::PARAM_TYPE]) ? $contextData[self::PARAM_TYPE] : '';
        $label = isset($contextData[self::PARAM_LABEL]) ? $contextData[self::PARAM_LABEL] : '';

        return new static($executionId, $executionContextId, $type, $label);
    }

    /**
     * @param $executionId
     * @throws InvalidArgumentException
     */
    private function validateExecutionId($executionId)
    {
        if (!is_string($executionId) || empty($executionId)) {
            throw new InvalidArgumentException('Execution ID value must be not empty string.');
        }
    }

    /**
     * @param $executionContextId
     * @throws InvalidArgumentException
     */
    private function validateExecutionContextId($executionContextId)
    {
        if (!is_string($executionContextId) || empty($executionContextId)) {
            throw new InvalidArgumentException('Execution context ID value must be not empty string.');
        }
    }

    /**
     * @return string
     */
    public function getExecutionId()
    {
        return $this->executionId;
    }

    /**
     * @param string $executionId
     * @throws InvalidArgumentException
     */
    public function setExecutionId($executionId)
    {
        $this->validateExecutionId($executionId);

        $this->executionId = $executionId;
    }

    /**
     * @param string $contextId
     * @throws InvalidArgumentException
     */
    public function setExecutionContextId($contextId)
    {
        $this->validateExecutionContextId($contextId);

        $this->executionContextId = $contextId;
    }

    /**
     * @return string
     */
    public function getExecutionContextId()
    {
        return $this->executionContextId;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
            self::PARAM_LABEL => $this->getLabel()
        ];
    }
}
