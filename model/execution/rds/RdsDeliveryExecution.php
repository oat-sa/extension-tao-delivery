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
 * Copyright (c) 2013-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\model\execution\rds;

use core_kernel_classes_Resource;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * RDS Delivery Execution model
 *
 * @author Péter Halász <peter@taotesting.com>
 */
class RdsDeliveryExecution implements DeliveryExecutionInterface
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $label;

    /** @var core_kernel_classes_Resource */
    private $delivery;

    /** @var core_kernel_classes_Resource */
    private $state;

    /** @var string */
    private $userIdentifier;

    /** @var string */
    private $startTime;

    /** @var string */
    private $finishTime;

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param int $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
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
     * @return core_kernel_classes_Resource
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param core_kernel_classes_Resource $delivery
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return core_kernel_classes_Resource
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param core_kernel_classes_Resource $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     * @param string $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param string $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return string
     */
    public function getFinishTime()
    {
        return $this->finishTime;
    }

    /**
     * @param string $finishTime
     */
    public function setFinishTime($finishTime)
    {
        $this->finishTime = $finishTime;
    }
}