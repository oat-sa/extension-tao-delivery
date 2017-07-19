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
use common_Logger;
use common_session_SessionManager;
use core_kernel_classes_Property;
use oat\oatbox\user\User;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\RuntimeService;
use oat\taoDelivery\model\container\ExecutionContainer;
use taoResultServer_models_classes_ResultServerStateFull;

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
     * Get resumable (active) deliveries.
     * @param User $user User instance. If not given then all deliveries will be returned regardless of user URI.
     * @return \oat\taoDelivery\model\execution\DeliveryExecution []
     */
    public function getResumableDeliveries(User $user)
    {
        $deliveryExecutionService = ServiceProxy::singleton();
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
     * initalize the resultserver for a given execution
     * @param \core_kernel_classes_Resource $compiledDelivery
     * @param string $executionIdentifier
     * @throws \common_Exception
     */
    public function initResultServer($compiledDelivery, $executionIdentifier){

        //starts or resume a taoResultServerStateFull session for results submission

        //retrieve the result server definition
        $resultServer = $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        //callOptions are required in the case of a LTI basic storage

        taoResultServer_models_classes_ResultServerStateFull::singleton()->initResultServer($resultServer->getUri());

        //a unique identifier for data collected through this delivery execution
        //in the case of LTI, we should use the sourceId


        taoResultServer_models_classes_ResultServerStateFull::singleton()->spawnResult($executionIdentifier, $executionIdentifier);
         common_Logger::i("Spawning/resuming result identifier related to process execution ".$executionIdentifier);
        //set up the related test taker
        //a unique identifier for the test taker
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedTestTaker(common_session_SessionManager::getSession()->getUserUri());

         //a unique identifier for the delivery
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedDelivery($compiledDelivery->getUri());
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
