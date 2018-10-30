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

namespace oat\taoDelivery\models\classes\execution\event;

use oat\oatbox\event\Event;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

class DeliveryExecutionReactivated implements Event, DeliveryExecutionAwareInterface
{
    const LOG_KEY = 'TEST_REACTIVATED';

    private $deliveryExecution;
    private $user;
    private $reason;


    /**
     * QtiMoveEvent constructor.
     * @param DeliveryExecutionInterface $deliveryExecution
     * @param User $user
     * @param null $reason
     */
    public function __construct(DeliveryExecutionInterface $deliveryExecution, User $user, $reason = null)
    {
        $this->deliveryExecution = $deliveryExecution;
        $this->user = $user;
        $this->reason = $reason;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * @return \oat\taoDelivery\model\execution\DeliveryExecution|DeliveryExecutionInterface
     */
    public function getDeliveryExecution()
    {
        return $this->deliveryExecution;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


}
