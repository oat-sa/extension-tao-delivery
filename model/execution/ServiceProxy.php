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

use common_exception_NoImplementation;
use common_exception_NotFound;
use common_Logger;
use common_session_SessionManager;
use core_kernel_classes_Resource;
use core_kernel_users_GenerisUser;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\event\EventManager;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDeleteRequest;
use tao_models_classes_Service;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery

 */
class ServiceProxy extends tao_models_classes_Service implements DeliveryExecutionService
{
    public const CONFIG_KEY = 'execution_service';

    public function setImplementation(DeliveryExecutionService $implementation)
    {
        $this->getServiceLocator()->register(self::SERVICE_ID, $implementation);
    }

    protected function getImplementation(): DeliveryExecutionService
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(self::SERVICE_ID);
    }

    /**
     * (non-PHPdoc)
     * @see DeliveryExecutionService::getUserExecutions()
     */
    public function getUserExecutions(core_kernel_classes_Resource $assembly, $userUri)
    {
        return $this->getImplementation()->getUserExecutions($assembly, $userUri);
    }

    /**
     * (non-PHPdoc)
     * @see DeliveryExecutionService::getDeliveryExecutionsByStatus()
     */
    public function getDeliveryExecutionsByStatus($userUri, $status)
    {
        return $this->getImplementation()->getDeliveryExecutionsByStatus($userUri, $status);
    }

    public function getActiveDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, DeliveryExecutionInterface::STATE_ACTIVE);
    }

    public function getPausedDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, DeliveryExecutionInterface::STATE_PAUSED);
    }

    public function getFinishedDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, DeliveryExecutionInterface::STATE_FINISHED);
    }

    /**
     * @deprecated
     * (non-PHPdoc)
     * @see DeliveryExecutionService::initDeliveryExecution()
     * @throws \common_exception_Error
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $user)
    {
        if (is_string($user)) {
            common_Logger::w('Deprecated use of initDeliveryExecution()');
            $sessionUser = common_session_SessionManager::getSession()->getUser();
            if ($user == $sessionUser->getIdentifier()) {
                $user = $sessionUser;
            } else {
                $generisUser = new core_kernel_classes_Resource($user);
                if ($generisUser->exists()) {
                    $user = new core_kernel_users_GenerisUser($generisUser);
                } else {
                    throw new common_exception_NotFound('Unable to find User "' . $user . '"');
                }
            }
        }
        $deliveryExecution = $this->getImplementation()->initDeliveryExecution($assembly, $user->getIdentifier());
        $eventManager = ServiceManager::getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->trigger(new DeliveryExecutionCreated($deliveryExecution, $user));
        return $deliveryExecution;
    }


    /**
     * (non-PHPdoc)
     * @see DeliveryExecutionService::getDeliveryExecution()
     */
    public function getDeliveryExecution($identifier)
    {
        return $this->getImplementation()->getDeliveryExecution($identifier);
    }

    /**
     * Implemented in the monitoring interface
     *
     * @param core_kernel_classes_Resource $compiled
     * @return DeliveryExecution[] executions for a single compilation
     * @throws \common_exception_Error
     * @throws common_exception_NoImplementation
     */
    public function getExecutionsByDelivery(core_kernel_classes_Resource $compiled)
    {
        if (!$this->implementsMonitoring()) {
            throw new common_exception_NoImplementation(
                get_class($this->getImplementation())
                    . ' does not implement \oat\taoDelivery\model\execution\Monitoring'
            );
        }
        return $this->getImplementation()->getExecutionsByDelivery($compiled);
    }

    /**
     * Whenever or not the current implementation supports monitoring
     *
     * @return boolean
     * @throws \common_exception_Error
     */
    public function implementsMonitoring()
    {
        return $this->getImplementation() instanceof Monitoring;
    }

    /**
     * @inheritdoc
     */
    public function deleteDeliveryExecutionData(DeliveryExecutionDeleteRequest $request)
    {
        return $this->getImplementation()->deleteDeliveryExecutionData($request);
    }
}
