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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoDelivery\model\execution;

use common_exception_NoImplementation;
use core_kernel_classes_Resource;
use JsonSerializable;
use oat\oatbox\service\ServiceManagerAwareInterface;
use oat\oatbox\service\ServiceManagerAwareTrait;
use oat\taoDelivery\model\fields\DeliveryFieldsService;

class DeliveryExecution implements
    DeliveryExecutionInterface,
    ServiceManagerAwareInterface,
    JsonSerializable,
    OriginalIdAwareDeliveryExecutionInterface
{
    use ServiceManagerAwareTrait;

    /**
     * @var DeliveryExecutionInterface
     */
    private $implementation;

    public function __construct(DeliveryExecutionInterface $implementation)
    {
        $this->setImplementation($implementation);
    }

    public function setImplementation(DeliveryExecutionInterface $implementation)
    {
        $this->implementation = $implementation;
    }

    /**
     * @return DeliveryExecutionInterface
     */
    public function getImplementation()
    {
        return $this->implementation;
    }

    /**
     * Returns the identifier of the delivery execution
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getImplementation()->getIdentifier();
    }

    /**
     * @inheritDoc
     */
    public function getOriginalIdentifier(): string
    {
        return  $this->getImplementation()->getOriginalIdentifier();
    }

    /**
     * Returns a human readable test representation of the delivery execution
     * Should respect the current user's language
     *
     * @return string
     * @throws \common_exception_NotFound
     */
    public function getLabel()
    {
        /** @var DeliveryFieldsService $deliveryFieldsService */
        $deliveryFieldsService = $this->getServiceLocator()->get(DeliveryFieldsService::SERVICE_ID);
        $label = $deliveryFieldsService->getLabel(
            $this->getImplementation()->getDelivery(),
            $this->getImplementation()->getLabel()
        );
        return $label;
    }

    /**
     * Returns when the delivery execution was started
     * @throws \common_exception_NotFound
     */
    public function getStartTime()
    {
        return $this->getImplementation()->getStartTime();
    }

    /**
     * Returns when the delivery execution was finished
     * or null if not yet finished
     * @throws \common_exception_NotFound
     */
    public function getFinishTime()
    {
        return $this->getImplementation()->getFinishTime();
    }

    /**
     * Returns the delivery execution state as resource
     * @throws \common_exception_NotFound
     */
    public function getState()
    {
        return $this->getImplementation()->getState();
    }

    /**
     *
     * @param string $state
     * @return boolean success
     */
    public function setState($state)
    {
        /** @var \oat\taoDelivery\model\execution\AbstractStateService $stateService */
        $stateService = $this->getServiceLocator()->get(StateServiceInterface::SERVICE_ID);
        $result = $stateService->legacyTransition($this, $state);
        return $result;
    }

    /**
     * Returns the delivery execution delivery as resource
     *
     * @return core_kernel_classes_Resource
     * @throws \common_exception_NotFound
     */
    public function getDelivery()
    {
        return $this->getImplementation()->getDelivery();
    }

    /**
     * Returns the delivery executions user identifier
     *
     * @return string
     * @throws \common_exception_NotFound
     */
    public function getUserIdentifier()
    {
        return $this->getImplementation()->getUserIdentifier();
    }

    /**
     * Calls the named method which is not a class method.
     * Do not call this method.
     * @param string $name the method name
     * @param array $parameters method parameters
     * @return mixed the method return value
     */
    public function __call($name, $parameters)
    {
        return call_user_func_array([$this->getImplementation(), $name], $parameters);
    }

    /**
     * Returns the delivery id session key.
     *
     * @param $deliveryExecutionId
     *
     * @return string
     */
    public static function getDeliveryIdSessionKey($deliveryExecutionId)
    {
        return 'deliveryIdForDeliveryExecution:' . $deliveryExecutionId;
    }

    public function jsonSerialize(): array
    {
        if ($this->getImplementation() instanceof JsonSerializable) {
            return $this->getImplementation()->jsonSerialize();
        }

        throw new common_exception_NoImplementation(
            'Delivery Execution model does not implement json serialise method'
        );
    }
}
