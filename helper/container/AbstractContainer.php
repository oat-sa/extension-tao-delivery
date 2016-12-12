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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoDelivery\helper\container;

use oat\taoDelivery\model\execution\DeliveryExecution;
use \oat\taoDelivery\model\DeliveryContainer as DeliveryContainerInterface;
use oat\oatbox\Configurable;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract container to simplify the development of
 * simple containers
 */
abstract class AbstractContainer extends Configurable implements DeliveryContainerInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    private $data = array();
    
    /**
     * @var DeliveryExecution
     */
    protected $deliveryExecution;
    
    /**
     * DeliveryContainer constructor.
     * @param DeliveryExecution $deliveryExecution
     * @param array $options
     */
    public function __construct(DeliveryExecution $deliveryExecution, $options = [])
    {
        $this->setOptions($options);
        $this->deliveryExecution = $deliveryExecution;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\DeliveryContainer::setData()
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    /**
     * @return \Renderer
     */
    public function getContainerHeader()
    {
        $renderer = new \Renderer($this->getHeaderTemplate());
        $renderer->setMultipleData($this->data);
        return $renderer;
    }
    
    /**
     * @return \Renderer
    */
    public function getContainerBody()
    {
        $renderer = new \Renderer($this->getBodyTemplate());
        $renderer->setMultipleData($this->data);
        return $renderer;
    }

    /**
     * Delegated constructor
     * @return void
     */
    public function init()
    {
        $service = $this->getServiceLocator()->get(\taoDelivery_models_classes_DeliveryServerService::CONFIG_ID);
        $this->setData('deliveryExecution', $this->deliveryExecution);
        $this->setData('deliveryServerConfig', $service->getJsConfig($this->deliveryExecution->getDelivery()));
    }

    /**
     * Returns the path to the header template
     *
     * @return string
     */
    protected abstract function getHeaderTemplate();

    /**
     * Returns the path to the body template
     *
     * @return string
     */
    protected abstract function getBodyTemplate();

}
