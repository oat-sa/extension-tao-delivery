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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoDelivery\model\container\delivery;

use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\DeliveryContainerService;
use oat\taoDelivery\model\container\execution\ExecutionClientContainer;
/**
 * Legacy Client Container, requires to do its own data retrieval
 */
class LegacyClientContainer extends AbstractContainer
{
    private $serviceCallParam = [];
    
    /**
     * Return the URI of the test for the delivery
     * @param DeliveryExecution $execution
     * @return string
     */
    public function getSourceTest(DeliveryExecution $execution)
    {
        $containerService = $this->getServiceLocator()->get(DeliveryContainerService::SERVICE_ID);
        return $containerService->getTestDefinition($execution);
    }
    
    /**
     * Return the id of the storage directory for the public content
     * @param DeliveryExecution $execution
     * @return string
     */
    public function getPublicDirId(DeliveryExecution $execution)
    {
        list($private, $public) = explode('|', $this->getServiceCallParam($execution));
        return $public;
    }
    
    /**
     * Return the id of the storage directory for the private content
     * @param DeliveryExecution $execution
     * @return string
     */
    public function getPrivateDirId(DeliveryExecution $execution)
    {
        list($private, $public) = explode('|', $this->getServiceCallParam($execution));
        return $private;
    }
    
    private function getServiceCallParam(DeliveryExecution $execution)
    {
        if (!isset($this->serviceCallParam[$execution->getIdentifier()])) {
            $containerService = $this->getServiceLocator()->get(DeliveryContainerService::SERVICE_ID);
            $this->serviceCallParam[$execution->getIdentifier()] = $containerService->getTestCompilation($execution);
        }
        return $this->serviceCallParam[$execution->getIdentifier()];
    }

    /**
     * {@inheritDoc}
     * @see \oat\taoDelivery\model\container\delivery\AbstractContainer::getExecutionContainer()
     */
    public function getExecutionContainer(DeliveryExecution $execution)
    {
        $container = new ExecutionClientContainer($execution);
        $containerService = $this->getServiceLocator()->get(DeliveryContainerService::SERVICE_ID);

        // set the test parameters
        $container->setData('testDefinition', $this->getSourceTest($execution));
        $container->setData('testCompilation', $this->getPrivateDirId($execution).'|'.$this->getPublicDirId($execution));
        $container->setData('providers', $containerService->getProviders($execution));
        $container->setData('plugins', $containerService->getPlugins($execution));
        $container->setData('bootstrap', $containerService->getBootstrap($execution));
        $container->setData('serviceCallId', $execution->getIdentifier());
        $container->setData('deliveryExecution', $execution->getIdentifier());
        $container->setData('deliveryServerConfig', []);
        return $container;
    }
}
