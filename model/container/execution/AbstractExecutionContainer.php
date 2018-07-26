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
namespace oat\taoDelivery\model\container\execution;

use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\container\ExecutionContainer;
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 * Abstract container to simplify the development of
 * simple containers
 */
abstract class AbstractExecutionContainer implements ExecutionContainer
{
    /**
     * @var array
     */
    private $data = array();

    /**
     * @var DeliveryExecution
     */
    private $deliveryExecution;

    /**
     * DeliveryContainer constructor.
     * @param DeliveryExecution $deliveryExecution
     */
    public function __construct(DeliveryExecution $deliveryExecution)
    {
        $this->deliveryExecution = $deliveryExecution;
    }

    /**
     * {@inheritDoc}
     * @see \oat\taoDelivery\model\container\ExecutionContainer::setData()
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
     * Returns the path to the header template
     * @return string
     */
    protected abstract function getHeaderTemplate();

    /**
     * Returns the path to the body template
     * @return string
     */
    protected abstract function getBodyTemplate();
}
