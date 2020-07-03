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

use common_exception_NotFound;
use oat\oatbox\event\Event;
use oat\oatbox\event\EventManager;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\User;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionReactivated;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState as DeliveryExecutionStateEvent;

/**
 * Class AbstractStateService
 * @package oat\taoDelivery
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
abstract class AbstractStateService extends ConfigurableService implements StateServiceInterface
{
    use LoggerAwareTrait;

    public const OPTION_REACTIVABLE_STATES = 'reactivableStates';

    private const DEFAULT_REACTIVABLE_STATES = [
        DeliveryExecutionInterface::STATE_TERMINATED,
    ];

    private const INTERACTIVE_STATES = [
        DeliveryExecutionInterface::STATE_ACTIVE,
        DeliveryExecutionInterface::STATE_PAUSED,
    ];

    /**
     * Legacy function to ensure all calls to setState use
     * the correct transition instead
     *
     * @param DeliveryExecution $deliveryExecution
     * @param string $state
     *
     * @return bool
     */
    abstract public function legacyTransition(DeliveryExecution $deliveryExecution, $state);

    /**
     * Get the status new delivery executions should be started with
     *
     * @param string $deliveryId
     * @param User $user
     *
     * @return string
     */
    abstract public function getInitialStatus($deliveryId, User $user);

    /**
     * @inheritDoc
     */
    public function createDeliveryExecution($deliveryId, User $user, $label)
    {
        $status = $this->getInitialStatus($deliveryId, $user);
        $deliveryExecution = $this->getStorageEngine()->spawnDeliveryExecution($label, $deliveryId, $user->getIdentifier(), $status);
        // trigger event
        $event = new DeliveryExecutionCreated($deliveryExecution, $user);
        $this->getEventManager()->trigger($event);

        return $deliveryExecution;
    }

    /**
     * @inheritDoc
     */
    public function reactivateExecution(DeliveryExecution $deliveryExecution, $reason = null)
    {
        $executionState = $deliveryExecution->getState()->getUri();
        $result = false;

        if (in_array($executionState, $this->getReactivableStates(), true)) {
            $this->setState($deliveryExecution, DeliveryExecution::STATE_PAUSED, $reason);
            $result = true;
        }

        return $result;
    }

    /**
     * @param DeliveryExecution $deliveryExecution
     * @param string            $state
     * @param string|array|null $reason
     *
     * @return bool
     *
     * @throws common_exception_NotFound
     */
    protected function setState(DeliveryExecution $deliveryExecution, string $state, $reason = null): bool
    {
        $previousState = $deliveryExecution->getState()->getUri();
        if ($previousState === $state) {
            $this->logWarning('Delivery execution ' . $deliveryExecution->getIdentifier() . ' already in state ' . $state);

            return false;
        }

        $result = $deliveryExecution->getImplementation()->setState($state);

        $this->emitEvent(new DeliveryExecutionStateEvent($deliveryExecution, $state, $previousState));
        $this->logDebug(sprintf('DeliveryExecutionState from %s to %s triggered', $previousState, $state));

        if (!$this->isStateInteractive($previousState) && $this->isStateInteractive($state)) {
            $this->emitEvent(
                new DeliveryExecutionReactivated(
                    $deliveryExecution,
                    $this->getSessionService()->getCurrentUser(),
                    $reason
                )
            );
        }

        return $result;
    }

    protected function getStorageEngine(): DeliveryExecutionService
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(DeliveryExecutionService::SERVICE_ID);
    }

    private function getSessionService(): SessionService
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(SessionService::SERVICE_ID);
    }

    private function getEventManager(): EventManager
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(EventManager::SERVICE_ID);
    }

    private function emitEvent(Event $event): void
    {
        $this->getEventManager()->trigger($event);
    }

    private function getReactivableStates(): array
    {
        if (!$this->hasOption(self::OPTION_REACTIVABLE_STATES)) {
            return self::DEFAULT_REACTIVABLE_STATES;
        }

        return $this->getOption(self::OPTION_REACTIVABLE_STATES);
    }

    private function isStateInteractive(string $state): bool
    {
        return in_array($state, self::INTERACTIVE_STATES, true);
    }
}
