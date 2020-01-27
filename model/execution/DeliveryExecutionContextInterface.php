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
    const PARAM_EXECUTION_ID = 'execution_id';
    const PARAM_CONTEXT_ID = 'context_id';
    const PARAM_TYPE = 'type';
    const PARAM_LABEL = 'label';

    /**
     * @param string $executionId
     */
    public function setExecutionId($executionId);

    /**
     * @return string
     */
    public function getExecutionId();

    /**
     * @param string $contextId
     */
    public function setExecutionContextId($contextId);

    /**
     * @return string
     */
    public function getExecutionContextId();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $label
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabel();
}
