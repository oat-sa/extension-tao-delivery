<?php

namespace oat\taoDelivery\model\execution;

use core_kernel_classes_Resource;

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