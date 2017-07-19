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
namespace oat\taoDelivery\model\container;

use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\execution\DeliveryServerService;
use oat\taoDelivery\model\RuntimeService;
use oat\taoDelivery\model\AssignmentService;
use oat\taoDelivery\model\container\delivery\LegacyServiceContainer;
use oat\taoDelivery\model\container\delivery\LegacyClientContainer;
/**
 * LegacyRuntime Service that uses platform wide configuration
 * to determine runtime container
 */
class LegacyRuntime extends ConfigurableService implements RuntimeService
{
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\RuntimeService::getDeliveryContainer()
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function getDeliveryContainer($deliveryId)
    {
        $dService = $this->getServiceLocator()->get(DeliveryServerService::SERVICE_ID);
        $containerClass = $dService->getOption('deliveryContainer');
        switch ($containerClass) {
            case 'oat\\taoDelivery\\helper\\container\\DeliveryServiceContainer':
                $container = new LegacyServiceContainer();
                $container->setServiceLocator($this->getServiceLocator());
                break;
            case 'oat\\taoDelivery\\helper\\container\\DeliveryClientContainer':
                $container = new LegacyClientContainer();
                $container->setServiceLocator($this->getServiceLocator());
                break;
            default:
                throw new \common_exception_InconsistentData('Unknown container "'.$containerClass.'"');
        }
        return $container;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\RuntimeService::getRuntime()
     */
    public function getRuntime($deliveryId)
    {
        return $this->getServiceLocator()->get(AssignmentService::SERVICE_ID)->getRuntime($deliveryId);
    }
}
