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
 * Copyright (c) 2015-2021 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoDelivery\models\classes\execution\event;

use oat\oatbox\event\Event;
use oat\tao\model\Context\ContextInterface;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * Event should be triggered after changing delivery execution state.
 *
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
class DeliveryExecutionState implements Event
{
    /**
     * @var DeliveryExecutionInterface delivery execution instance
     */
    private $deliveryExecution;
    /**
     * @var string state name
     */
    private $state;
    /**
     * @var string previous state name
     */
    private $prevState;

    /** @var ContextInterface|null */
    private $context;

    public function __construct(
        DeliveryExecutionInterface $deliveryExecution,
        string $state,
        string $prevState = null,
        ContextInterface $context = null
    ) {
        $this->deliveryExecution = $deliveryExecution;
        $this->state = $state;
        $this->prevState = $prevState;
        $this->context = $context;
    }

    /**
     * @return DeliveryExecutionInterface
     */
    public function getDeliveryExecution()
    {
        return $this->deliveryExecution;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return null|string
     */
    public function getPreviousState()
    {
        return $this->prevState;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }

    public function getContext(): ?ContextInterface
    {
        return $this->context;
    }
}
