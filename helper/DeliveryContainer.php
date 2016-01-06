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

namespace oat\taoDelivery\helper;

use oat\tao\helpers\Template;
use oat\taoDelivery\model\execution\DeliveryExecution;
use \oat\taoDelivery\model\DeliveryContainer as DeliveryContainerInterface;

abstract class DeliveryContainer extends \Renderer implements DeliveryContainerInterface
{
    /**
     * The path to the loader template
     * @var string
     */
    protected $loaderTemplate;
    
    /**
     * The path to the content template
     * @var string
     */
    protected $contentTemplate;

    /**
     * The name of the extension containing the loader template
     * @var string
     */
    protected $loaderTemplateExtension = 'taoDelivery';
    
    /**
     * The name of the extension containing the content template
     * @var string
     */
    protected $contentTemplateExtension = 'taoDelivery';

    /**
     * @var DeliveryExecution
     */
    protected $deliveryExecution;
    
    /**
     * DeliveryContainer constructor.
     * @param DeliveryExecution $deliveryExecution
     */
    public function __construct(DeliveryExecution $deliveryExecution)
    {
        $tpl = Template::getTemplate($this->loaderTemplate, $this->loaderTemplateExtension);
        parent::__construct($tpl);
        
        $this->deliveryExecution = $deliveryExecution;
        $this->init();
    }

    /**
     * Delegated constructor
     * @return void
     */
    abstract protected function init();

    /**
     * @return string
     */
    public function getContentTemplate()
    {
        return $this->contentTemplate;
    }

    /**
     * @return string
     */
    public function getLoaderTemplate()
    {
        return $this->loaderTemplate;
    }

    /**
     * @return string
     */
    public function getLoaderTemplateExtension()
    {
        return $this->loaderTemplateExtension;
    }

    /**
     * @return string
     */
    public function getContentTemplateExtension()
    {
        return $this->contentTemplateExtension;
    }
}
