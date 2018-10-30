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
use oat\generis\model\OntologyAwareTrait;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * RDS Delivery Execution model
 *
 * @author Péter Halász <peter@taotesting.com>
 */
class RdsDeliveryExecution implements DeliveryExecutionInterface
{
    use OntologyAwareTrait;

    /** @var string */
    private $identifier;

    /** @var string */
    private $label;

    /** @var string */
    private $delivery;

    /** @var string */
    private $state;

    /** @var string */
    private $userIdentifier;

    /** @var \DateTime */
    private $startTime;

    /** @var \DateTime */
    private $finishTime;

    /** @var RdsDeliveryExecutionService */
    private $service;

    public function __construct(RdsDeliveryExecutionService $service)
    {
        $this->service = $service;
    }

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
     * @return core_kernel_classes_Resource|null
     * @throws \common_exception_Error
     */
    public function getDelivery()
    {
        if (!is_null($this->delivery)) {
            return $this->getResource($this->delivery);
        } else {
            return null;
        }
    }

    /**
     * @param string $delivery
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return core_kernel_classes_Resource|null
     * @throws \common_exception_Error
     */
    public function getState()
    {
        if (!is_null($this->state)) {
            return $this->getResource($this->state);
        } else {
            return null;
        }
    }

    /**
     * @param string $state
     * @return bool
     */
    public function setState($state)
    {
        $isUpdated = true;

        // Avoid any update in database when mapping the value
        if ($this->state !== null) {
            $isUpdated = $this->service->updateDeliveryExecutionState(
                $this->identifier,
                $this->state,
                $state
            );
        }

        $this->state = $state;

        return $isUpdated;
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
     * @return int
     */
    public function getStartTime()
    {
        if (!empty($this->startTime)) {
            return $this->startTime->getTimestamp();
        } else {
            return null;
        }
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return int
     */
    public function getFinishTime()
    {
        if (!empty($this->finishTime)) {
            return $this->finishTime->getTimestamp();
        } else {
            return null;
        }
    }

    /**
     * @param \DateTime $finishTime
     */
    public function setFinishTime($finishTime)
    {
        $this->finishTime = $finishTime;
    }
}
