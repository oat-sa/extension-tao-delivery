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

use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\event\EventManager;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery

 */
class taoDelivery_models_classes_execution_ServiceProxy extends tao_models_classes_Service
    implements taoDelivery_models_classes_execution_Service
{
    const CONFIG_KEY = 'execution_service';

    /**
     * @var taoDelivery_models_classes_execution_Service
     */
    private $implementation;

    public function setImplementation(taoDelivery_models_classes_execution_Service $implementation) {
        $this->implementation = $implementation;
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $ext->setConfig(self::CONFIG_KEY, $implementation);
    }

    protected function getImplementation() {
        if (is_null($this->implementation)) {
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
            $this->implementation = $ext->getConfig(self::CONFIG_KEY);
            if (!$this->implementation instanceof taoDelivery_models_classes_execution_Service) {
                throw new common_exception_Error('No implementation for '.__CLASS__.' found');
            }
        }
        return $this->implementation;
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getUserExecutions()
     */
    public function getUserExecutions(core_kernel_classes_Resource $assembly, $userUri) {
        return $this->getImplementation()->getUserExecutions($assembly, $userUri);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecutionsByStatus()
     */
    public function getDeliveryExecutionsByStatus($userUri, $status) {
        return $this->getImplementation()->getDeliveryExecutionsByStatus($userUri, $status);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getActiveDeliveryExecutions()
     */
    public function getActiveDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, DeliveryExecution::STATE_ACTIVE);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getPausedDeliveryExecutions()
     */
    public function getPausedDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, DeliveryExecution::STATE_PAUSED);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getFinishedDeliveryExecutions()
     */
    public function getFinishedDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, DeliveryExecution::STATE_FINISHIED);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::initDeliveryExecution()
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
                    throw new common_exception_NotFound('Unable to find User "'.$user.'"');
                }
            }
        }
        $deliveryExecution = $this->getImplementation()->initDeliveryExecution($assembly, $user->getIdentifier());
        $eventManager = ServiceManager::getServiceManager()->get(EventManager::CONFIG_ID);
        $eventManager->trigger(new DeliveryExecutionCreated($deliveryExecution, $user));
        return $deliveryExecution;
    }


    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecution()
     */
    public function getDeliveryExecution($identifier)
    {
        return $this->getImplementation()->getDeliveryExecution($identifier);
    }

    /**
     * Implemented in the monitoring interface
     *
     * @param core_kernel_classes_Resource $compiled
     * @return int the ammount of executions for a single compilation
     */
    public function getExecutionsByDelivery(core_kernel_classes_Resource $compiled)
    {
        if (!$this->implementsMonitoring()) {
            throw new common_exception_NoImplementation(get_class($this->getImplementation()).' does not implement taoDelivery_models_classes_execution_Monitoring');
        }
        return $this->getImplementation()->getExecutionsByDelivery($compiled);
    }

    /**
     * Whenever or not the current implementation supports monitoring
     *
     * @return boolean
     */
    public function implementsMonitoring() {
        return $this->getImplementation() instanceof taoDelivery_models_classes_execution_Monitoring;
    }
}
