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
    const CONFIG_ID = 'taoDelivery/deliveryServer';

    const SERVICE_ID = 'taoDelivery/deliveryServer';


    public static function singleton()
    {
        return ServiceManager::getServiceManager()->get(self::SERVICE_ID);
    }

    /**
     * Return the states a delivey execution can be resumed from
     * @return string[]
     */
    public function getResumableStates()
    {
        return [
            DeliveryExecution::STATE_ACTIVE
            ,DeliveryExecution::STATE_PAUSED
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
        $resumable = array();
        foreach ($this->getResumableStates() as $state) {
            foreach ($deliveryExecutionService->getDeliveryExecutionsByStatus($user->getIdentifier(), $state) as $execution) {
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
     * @param string $executionIdentifier
     */
    public function initResultServer($compiledDelivery, $executionIdentifier, $userUri) {
        $this->getServiceManager()->get(\oat\taoResultServer\models\classes\ResultServerService::SERVICE_ID)
            ->initResultServer($compiledDelivery, $executionIdentifier, $userUri);
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
     * @param $deliveryExecution
     * @return ResultStorageWrapper
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    public function getResultStoreWrapper($deliveryExecution)
    {
        //@todo check if cache should be used here
        if (!$deliveryExecution instanceof DeliveryExecutionInterface) {
            $deliveryExecution = ServiceProxy::singleton()->getDeliveryExecution($deliveryExecution);
        }
        $compiledDelivery = $deliveryExecution->getDelivery();
        /** @var ResultServerService $resultService */
        $resultService = $this->getServiceManager()->get(ResultServerService::SERVICE_ID);
        return new ResultStorageWrapper($deliveryExecution->getIdentifier(), $resultService->getResultStorage($compiledDelivery->getUri()));
    }
}
