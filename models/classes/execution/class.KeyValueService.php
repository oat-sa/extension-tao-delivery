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

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_execution_KeyValueService extends tao_models_classes_GenerisService
    implements taoDelivery_models_classes_execution_Service
{
    const DELIVERY_EXECUTION_PREFIX = 'kve_de_';
    
    const USER_ACTIVES_EXECUTIONS_PREFIX = 'kve_ae_';
    
    const USER_FINISHED_EXECUTIONS_PREFIX = 'kve_fe_';
    
    /**
     * @var common_persistence_KeyValuePersistence
     */
    private $persistance;
    
    protected function __construct() {
        parent::__construct();
        $this->persistance = common_persistence_KeyValuePersistence::getPersistence('deliveryExecution');
    }
    
    /**
     * NOT IMPLEMENTED
     * 
     * @param core_kernel_classes_Resource $compiled
     * @return number
     */
    public function getTotalExecutionCount(core_kernel_classes_Resource $compiled)
    {
        return 0;
    }
    
    public function getUserExecutionCount(core_kernel_classes_Resource $compiled, $userUri)
    {
        $activ = $this->getActiveDeliveryExecutions($userUri);
        $finished = $this->getFinishedDeliveryExecutions($userUri);
        
        $returnValue = 0;
        foreach (array_merge($activ, $finished) as $de) {
            if ($compiled->equals($de->getDelivery())) {
                $returnValue++;
            }
        }
        return $returnValue;
    }

    /**
     * Returns all activ Delivery Executions of a User
     *
     * @param unknown $userUri            
     * @return Ambigous <multitype:, array>
     */
    public function getActiveDeliveryExecutions($userUri)
    {
        $returnValue = array();
        $data = $this->persistance->get(self::USER_ACTIVES_EXECUTIONS_PREFIX.$userUri);
        $keys = $data !== false ? json_decode($data) : array();
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $returnValue[$key] = $this->getDeliveryExecution($key);
            }
        } else {
            common_Logger::w('Non array "'.gettype($keys).'" received as active Delivery Keys for user '.$userUri);
        }
        
        return $returnValue;
    }
        
    /**
     * Returns all finished Delivery Executions of a User
     *
     * @param unknown $userUri            
     * @return Ambigous <multitype:, array>
     */
    public function getFinishedDeliveryExecutions($userUri)
    {
        $returnValue = array();
        $data = $this->persistance->get(self::USER_FINISHED_EXECUTIONS_PREFIX.$userUri);
        $keys = $data !== false ? json_decode($data) : array();
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $returnValue[$key] = $this->getDeliveryExecution($key);
            }
        } else {
            common_Logger::w('Non array "'.gettype($keys).'" received as finished Delivery Keys for user '.$userUri);
        }
        
        return $returnValue;
    }
    
    /**
     * Generate a new delivery execution
     * 
     * @param core_kernel_classes_Resource $compiled
     * @param string $userUri
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $compiled, $userUri)
    {
        $deliveryExecution = taoDelivery_models_classes_execution_KVDeliveryExecution::spawn($userUri, $compiled);
        
        $activeExecutions = $this->getActiveDeliveryExecutions($userUri);
        $activeExecutions[$deliveryExecution->getIdentifier()] = $deliveryExecution;
        $this->setActiveDeliveryExecutions($userUri, $activeExecutions);
        
        return $deliveryExecution;
    }
    
    public function getData($deliveryExecutionId) {
        $dataString = $this->persistance->get($deliveryExecutionId);
        $data = json_decode($dataString, true);
        return $data;
    }
    
   /**
    * Finishes a delivery execution
    *
    * @param core_kernel_classes_Resource $deliveryExecution
    * @return boolean success
    */
    public function finishDeliveryExecution(taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution)
    {
        if (!$deliveryExecution instanceof taoDelivery_models_classes_execution_KVDeliveryExecution) {
            throw new common_exception_Error('Delviery Execution Implementation mismatch, got '.get_class($deliveryExecution).' in '.__CLASS__);
        }
        $currentStatus = $deliveryExecution->getStatus();
        if ($currentStatus->getUri() == INSTANCE_DELIVERYEXEC_FINISHED) {
            throw new common_Exception('Delivery execution '.$deliveryExecution->getUri().' has laready been finished');
        }
        $userId = $deliveryExecution->getUserIdentifier();
         
        $activeExecutions = $this->getActiveDeliveryExecutions($userId);
        unset($activeExecutions[$deliveryExecution->getIdentifier()]);
        $this->setActiveDeliveryExecutions($userId, $activeExecutions);
        
        $finishedExecutions = $this->getFinishedDeliveryExecutions($userId);
        $finishedExecutions[] = $deliveryExecution;
        $this->setFinishedDeliveryExecutions($userId, $finishedExecutions);
        
        $deliveryExecution->setFinished();
        return true;
    }
        
    public function getDeliveryExecution($identifier) {
        return new taoDelivery_models_classes_execution_KVDeliveryExecution($identifier);
    }
    
    private function setActiveDeliveryExecutions($userUri, $executions)
    {
        $keys = array();
        foreach ($executions as $execution) {
            $keys[] = $execution->getIdentifier();
        }
        return $this->persistance->set(self::USER_ACTIVES_EXECUTIONS_PREFIX.$userUri, json_encode($keys));
    }
    
    private function setFinishedDeliveryExecutions($userUri, $executions)
    {
        $keys = array();
        foreach ($executions as $execution) {
            $keys[] = $execution->getIdentifier();
        }
        return $this->persistance->set(self::USER_FINISHED_EXECUTIONS_PREFIX.$userUri, json_encode($keys));
    }    
}
