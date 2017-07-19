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

namespace oat\taoDelivery\models\classes\execution\event;

use oat\oatbox\event\Event;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * Event triggered whenever a new delivery execution is initialised
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
class DeliveryExecutionCreated implements Event, DeliveryExecutionAwareInterface
{
    const EVENT_NAME = __CLASS__;

    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\event\Event::getName()
     */
    public function getName()
    {
        return self::EVENT_NAME;
    }

    /**
     * @var DeliveryExecutionInterface delivery execution instance
     */
    private $deliveryExecution;

    /**
     * @var User user for whom the execution was created
     */
    private $user;

    /**
     * DeliveryExecutionState constructor.
     * @param DeliveryExecutionInterface $deliveryExecution
     */
    public function __construct(DeliveryExecutionInterface $deliveryExecution, User $user)
    {
        $this->deliveryExecution = $deliveryExecution;
        $this->user = $user;
    }

    /**
     * Returns newly created delivery execution
     *
     * @return DeliveryExecutionInterface
     */
    public function getDeliveryExecution()
    {
        return $this->deliveryExecution;
    }

    /**
     * Returns the user for whom the delivery execution was created
     * @return \oat\oatbox\user\User
     */
    public function getUser()
    {
        return $this->user;
    }
}