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
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use oat\taoDelivery\model\DeliveryContainerService;
/**
 * Legacy Client Container, requires to do its own data retrieval
 */
class LegacyClientContainer extends DeliveryClientContainer 
{
    private $serviceCallParam = [];
    
    public function getSourceTest(DeliveryExecution $execution)
    {
        $containerService = $this->getServiceLocator()->get(DeliveryContainerService::CONFIG_ID);
        return $containerService->getTestDefinition($execution);
    }
    
    public function getPublicDirId(DeliveryExecution $execution)
    {
        list($private, $public) = explode('|', $this->getServiceCallParam($execution));
        return $public;
    }
    
    public function getPrivateDirId(DeliveryExecution $execution)
    {
        list($private, $public) = explode('|', $this->getServiceCallParam($execution));
        return $private;
    }
    
    private function getServiceCallParam(DeliveryExecution $execution)
    {
        if (!isset($this->serviceCallParam[$execution->getIdentifier()])) {
            $containerService = $this->getServiceLocator()->get(DeliveryContainerService::CONFIG_ID);
            $this->serviceCallParam[$execution->getIdentifier()] = $containerService->getTestCompilation($execution);
        }
        return $this->serviceCallParam[$execution->getIdentifier()];
    }
}
