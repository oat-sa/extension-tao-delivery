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

use common_exception_NotFound;
use core_kernel_classes_Resource;

/**
 * New interface for delivery executions
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
interface DeliveryExecutionInterface
{
    /**
     * Indicates that a test-taker is currently taking a delivery
     * @var string
     */
    const STATE_ACTIVE = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusActive';

    /**
     * Indicates that a delivery is in progress, but the test-taker is not actively taking it
     * @var string
     */
    const STATE_PAUSED = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusPaused';

    /**
     * Indicates that a delivery has been finished successfully and the results should be considered
     * @var string
     */
    const STATE_FINISHED = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusFinished';

    /**
     * @deprecated
     */
    const STATE_FINISHIED = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusFinished';

    /**
     * Indicates that a delivery has been terminated and the results might not be valid
     * @var string
     */
    const STATE_TERMINATED = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusTerminated';

    /**
     * Returns the identifier of the delivery execution
     *
     * @throws common_exception_NotFound
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns a human readable test representation of the delivery execution
     * Should respect the current user's language
     *
     * @throws common_exception_NotFound
     * @return string
     */
    public function getLabel();

    /**
     * Returns when the delivery execution was started
     *
     * @throws common_exception_NotFound
     * @return string
     */
    public function getStartTime();

    /**
     * Returns when the delivery execution was finished
     * or null if not yet finished
     *
     * @throws common_exception_NotFound
     * * @return string | null if the execution is not yet finished
     */
    public function getFinishTime();

    /**
     * Returns the delivery execution state as resource
     *
     * @throws common_exception_NotFound
     * @return core_kernel_classes_Resource
     */
    public function getState();

    /**
     *
     * @param string $state
     * @return boolean success
     */
    public function setState($state);

    /**
     * Returns the delivery execution delivery as resource
     *
     * @throws common_exception_NotFound
     * @return core_kernel_classes_Resource
     */
    public function getDelivery();

    /**
     * Returns the delivery executions user identifier
     *
     * @throws common_exception_NotFound
     * @return string
     */
    public function getUserIdentifier();
}
