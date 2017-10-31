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

use oat\oatbox\AbstractRegistry;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use oat\oatbox\Configurable;

class DeliveryContainerRegistry extends AbstractRegistry implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    
    protected function getExtension()
    {
        return $this->getServiceLocator()->get(\common_ext_ExtensionsManager::SERVICE_ID)->getExtensionById('taoDelivery');
    }
    
    protected function getConfigId()
    {
        return 'deliveryContainerRegistry';
    }
    
    public function registerContainerType($id, Configurable $container)
    {
        $this->set($id, $container);
    }
    
    public function fromJson($string) {
        $data = json_decode($string, true);
        if (!isset($data['container']) || !isset($data['params'])) {
            throw new \common_exception_InconsistentData('Invalid container json');
        }
        return $this->getDeliveryContainer($data['container'], $data['params']);
    }
    
    public function getDeliveryContainer($id, $params)
    {
        $container = $this->get($id);
        if (empty($container)) {
            throw new \common_exception_InconsistentData('Conainer "'.$id.'" not found.');
        }
        $container->setId($id);
        $container->setRuntimeParams($params);
        if ($container instanceof ServiceLocatorAwareInterface) {
            $container->setServiceLocator($this->getServiceLocator());
        }
        return $container;
    }
}