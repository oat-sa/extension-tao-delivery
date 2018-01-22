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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoDelivery\model\execution\Delete;

use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use qtism\runtime\tests\AssessmentTestSession;
use core_kernel_classes_Resource;

class DeliveryExecutionDeleteRequest
{
    /** @var core_kernel_classes_Resource */
    private $deliveryResource;

    /** @var DeliveryExecutionInterface */
    private $deliveryExecution;

    /** @var AssessmentTestSession */
    private $session;

    /**
     * DeliveryExecutionDeleteRequest constructor.
     * @param core_kernel_classes_Resource $deliveryResource
     * @param DeliveryExecutionInterface $deliveryExecution
     * @param AssessmentTestSession $session
     */
    public function __construct(
        core_kernel_classes_Resource $deliveryResource,
        DeliveryExecutionInterface $deliveryExecution,
        AssessmentTestSession $session = null //can  be null in case delivery was cancelled
    ) {
        $this->deliveryResource = $deliveryResource;
        $this->deliveryExecution = $deliveryExecution;
        $this->session = $session;
    }

    /**
     * @return DeliveryExecutionInterface
     */
    public function getDeliveryExecution()
    {
        return $this->deliveryExecution;
    }

    /**
     * @return AssessmentTestSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return core_kernel_classes_Resource
     */
    public function getDeliveryResource()
    {
        return $this->deliveryResource;
    }
}