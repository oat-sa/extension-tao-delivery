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

use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\RuntimeService;
use oat\taoDelivery\model\container\ExecutionContainer;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class taoDelivery_models_classes_DeliveryServerService extends ConfigurableService
{
    const CONFIG_ID = 'taoDelivery/deliveryServer';

    public static function singleton()
    {
        return ServiceManager::getServiceManager()->get(self::CONFIG_ID);
    }

    /**
     * Get resumable (active) deliveries.
     * @param User $user User instance. If not given then all deliveries will be returned regardless of user URI.
     * @return type
     */
    public function getResumableDeliveries(User $user)
    {
        $deliveryExecutionService = taoDelivery_models_classes_execution_ServiceProxy::singleton();
            $started = array_merge(
                $deliveryExecutionService->getActiveDeliveryExecutions($user->getIdentifier()),
                $deliveryExecutionService->getPausedDeliveryExecutions($user->getIdentifier())
            );
        
        $resumable = array();
        foreach ($started as $deliveryExecution) {
            $delivery = $deliveryExecution->getDelivery();
            if ($delivery->exists()) {
                $resumable[] = $deliveryExecution;
            }
        }
        return $resumable;
    }

    /**
     * Initialize the result server for a given execution
     *
     * @param $compiledDelivery
     * @param string $executionIdentifier
     */
    public function initResultServer($compiledDelivery, $executionIdentifier) {
        $this->getServiceManager()->get(\oat\taoResultServer\models\classes\ResultServerService::SERVICE_ID)
            ->initResultServer($compiledDelivery, $executionIdentifier);
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
}
