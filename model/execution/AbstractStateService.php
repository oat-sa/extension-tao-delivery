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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoDelivery\model\execution;

use oat\taoDelivery\models\classes\execution\DeliveryExecution as BaseDeliveryExecution;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState as DeliveryExecutionStateEvent;
use oat\oatbox\event\EventManager;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\user\User;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;

/**
 * Class AbstractStateService
 * @package oat\taoDelivery
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
abstract class AbstractStateService extends ConfigurableService implements StateServiceInterface
{
    use LoggerAwareTrait;

    /**
     * Legacy function to ensure all calls to setState use
     * the correct transition instead
     *
     * @param BaseDeliveryExecution $deliveryExecution
     * @param string $state
     * @return bool
     */
    abstract public function legacyTransition(DeliveryExecution $deliveryExecution, $state);

    /**
     * Get the status new delivery executions should be started with
     *
     * @param string $deliveryId
     * @param User $user
     * @return string
     */
    abstract public function getInitialStatus($deliveryId, User $user);

    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\execution\StateServiceInterface::createDeliveryExecution()
     */
    public function createDeliveryExecution($deliveryId, User $user, $label)
    {
        $status = $this->getInitialStatus($deliveryId, $user);
        $deliveryExecution = $this->getStorageEngine()->spawnDeliveryExecution($label, $deliveryId, $user->getIdentifier(), $status);
        // trigger event
        $event = new DeliveryExecutionCreated($deliveryExecution, $user);
        $this->getServiceLocator()->get(EventManager::CONFIG_ID)->trigger($event);
        return $deliveryExecution;
    }

    /**
     * @param BaseDeliveryExecution $deliveryExecution
     * @param string $state
     * @return bool
     */
    protected function setState(BaseDeliveryExecution $deliveryExecution, $state)
    {
        $prevState = $deliveryExecution->getState();
        if ($prevState->getUri() === $state) {
            $this->logDebug('Delivery execution '.$deliveryExecution->getIdentifier().' already in state '.$state);
            return false;
        }

        $result = $deliveryExecution->getImplementation()->setState($state);

        $event = new DeliveryExecutionStateEvent($deliveryExecution, $state, $prevState->getUri());
        $this->getServiceManager()->get(EventManager::SERVICE_ID)->trigger($event);
        $this->logInfo("DeliveryExecutionState from ".$prevState->getUri()." to ".$state." triggered");

        return $result;
    }

    /**
     * @return \taoDelivery_models_classes_execution_Service
     */
    protected function getStorageEngine()
    {
        return $this->getServiceLocator()->get(self::STORAGE_SERVICE_ID);
    }
}
