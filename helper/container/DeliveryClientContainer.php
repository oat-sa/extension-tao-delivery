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

namespace oat\taoDelivery\helper\container;

use oat\oatbox\service\ServiceManager;
use oat\tao\helpers\Template;
use oat\taoDelivery\model\AssignmentService;

/**
 * Class DeliveryClientContainer
 * @package oat\taoDelivery\helper
 */
class DeliveryClientContainer extends AbstractContainer
{
    /**
     * @inheritDoc
     */
    protected $loaderTemplate = 'DeliveryServer/container/client/loader.tpl';

    /**
     * @inheritDoc
     */
    protected $contentTemplate = 'DeliveryServer/container/client/template.tpl';

    /**
     * The name of the extension containing the loader template
     * @var string
     */
    protected $templateExtension = 'taoDelivery';
    
    /**
     * @inheritDoc
     */
    protected function init()
    {
        $delivery = $this->deliveryExecution->getDelivery();
        $runtime = ServiceManager::getServiceManager()->get(AssignmentService::CONFIG_ID)->getRuntime($delivery);
        $inputParameters = \tao_models_classes_service_ServiceCallHelper::getInputValues($runtime, array());

        // set the test parameters
        $this->setData('testDefinition', $inputParameters['QtiTestDefinition']);
        $this->setData('testCompilation', $inputParameters['QtiTestCompilation']);
        $this->setData('serviceCallId', $this->deliveryExecution->getIdentifier());

        // set the test runner config
        $config = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest')->getConfig('testRunner');
        $bootstrap = isset($config['bootstrap']) ? $config['bootstrap'] : [];
        $this->setData('bootstrap', $bootstrap);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\helper\container\AbstractContainer::getHeaderTemplate()
     */
    protected function getHeaderTemplate()
    {
        return Template::getTemplate($this->loaderTemplate, $this->templateExtension);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\helper\container\AbstractContainer::getBodyTemplate()
     */
    protected function getBodyTemplate()
    {
        return Template::getTemplate($this->contentTemplate, $this->templateExtension);        
    }
}
