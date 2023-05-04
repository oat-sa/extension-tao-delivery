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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDelivery\model\execution;

use common_Exception;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use oat\oatbox\user\User;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\RuntimeService;
use oat\taoDelivery\model\container\ExecutionContainer;
use oat\taoResultServer\models\classes\ResultServerService;
use oat\taoResultServer\models\classes\ResultStorageWrapper;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class DeliveryServerService extends ConfigurableService
{
    /** @deprecated */
    public const CONFIG_ID = 'taoDelivery/deliveryServer';

    public const SERVICE_ID = 'taoDelivery/deliveryServer';

    public const OPTION_RESULT_SERVER_SERVICE_FACTORY = 'resultServerServiceFactory';

    /** @var ResultServerService */
    public $resultServerService = null;

    public static function singleton()
    {
        return ServiceManager::getServiceManager()->get(self::SERVICE_ID);
    }

    /**
     * Return the states a delivery execution can be resumed from
     * @return string[]
     */
    public function getResumableStates()
    {
        return [
            DeliveryExecution::STATE_ACTIVE,
            DeliveryExecution::STATE_PAUSED
        ];
    }

    /**
     * Get resumable (active) deliveries.
     * @param User $user User instance. If not given then all deliveries will be returned regardless of user URI.
     * @return \oat\taoDelivery\model\execution\DeliveryExecution []
     */
    public function getResumableDeliveries(User $user)
    {
        $deliveryExecutionService = ServiceProxy::singleton();
        $resumable = [];

        foreach ($this->getResumableStates() as $state) {
            $executions = $deliveryExecutionService->getDeliveryExecutionsByStatus($user->getIdentifier(), $state);

            foreach ($executions as $execution) {
                $delivery = $execution->getDelivery();
                if ($delivery->exists()) {
                    $resumable[] = $execution;
                }
            }
        }

        return $resumable;
    }

    /**
     * Initialize the result server for a given execution
     *
     * @param $compiledDelivery
     * @param string $deliveryExecutionId
     */
    public function initResultServer($compiledDelivery, $deliveryExecutionId, $userUri)
    {
        $this->getResultServerService()->initResultServer($compiledDelivery, $deliveryExecutionId, $userUri);
    }

    /**
     * Returns the container for the delivery execution
     *
     * @param DeliveryExecution $deliveryExecution
     * @return ExecutionContainer
     * @throws common_Exception
     */
    public function getDeliveryContainer(DeliveryExecution $deliveryExecution)
    {
        $runtimeService = $this->getServiceLocator()->get(RuntimeService::SERVICE_ID);
        $deliveryContainer = $runtimeService->getDeliveryContainer($deliveryExecution->getDelivery()->getUri());
        return $deliveryContainer->getExecutionContainer($deliveryExecution);
    }

    /**
     * @param string $deliveryExecutionId id expectected, but still accepts delivery executions for backward
     *                                    compatibility
     */
    public function getResultStoreWrapper($deliveryExecutionId): ResultStorageWrapper
    {
        if ($deliveryExecutionId instanceof DeliveryExecutionInterface) {
            $deliveryExecutionId = $deliveryExecutionId->getIdentifier();
        }
        /** @var ResultServerService $resultService */
        $resultService = $this->getResultServerService();
        return new ResultStorageWrapper($deliveryExecutionId, $resultService->getResultStorage());
    }

    private function getResultServerService(): ResultServerService
    {
        if (null !== $this->resultServerService) {
            return $this->resultServerService;
        }

        $factory = $this->getOption(self::OPTION_RESULT_SERVER_SERVICE_FACTORY);

        if ($factory instanceof ResultServerServiceFactoryInterface) {
            if ($factory instanceof ServiceLocatorAwareInterface) {
                $this->propagate($factory);
            }
            $this->resultServerService = $factory->create();
        } else {
            $this->resultServerService = $this->getServiceLocator()->get(ResultServerService::SERVICE_ID);
        }

        return $this->resultServerService;
    }
}
