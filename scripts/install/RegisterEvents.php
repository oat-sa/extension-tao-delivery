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

namespace oat\taoDelivery\scripts\install;

use oat\oatbox\event\EventManager;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;
use oat\taoDelivery\model\execution\Counter\DeliveryExecutionCounterService;

/**
 * Class RegisterEvents
 * @package oat\taoDelivery\scripts\install
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class RegisterEvents extends \oat\oatbox\extension\InstallAction
{
    
    public function __invoke($params)
    {
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->attach(DeliveryExecutionState::class, [DeliveryExecutionCounterService::SERVICE_ID, 'executionStateChanged']);
        $this->getServiceManager()->register(EventManager::SERVICE_ID, $eventManager);
    }
}
