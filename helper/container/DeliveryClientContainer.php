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

use oat\tao\helpers\Template;
use common_ext_ExtensionsManager as ExtensionsManager;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoTests\models\runner\plugins\TestPlugin;
use oat\taoTests\models\runner\plugins\TestPluginService;
use oat\taoTests\models\runner\features\TestRunnerFeatureService;
use oat\taoDeliveryRdf\model\TestRunnerFeatures;
use oat\oatbox\service\ServiceManager;


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
        // set the test parameters
        $this->setData('testDefinition', $this->getOption('testDefinition'));
        $this->setData('testCompilation', $this->getOption('testCompilation'));
        $this->setData('plugins', $this->getPlugins($this->deliveryExecution));
        $this->setData('bootstrap', $this->getBootstrap($this->deliveryExecution));
        $this->setData('serviceCallId', $this->deliveryExecution->getIdentifier());
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

    /**
     * Get the list of active plugins for the current execution
     * @param DeliveryExecution $deliveryExecution
     * @return TestPlugin[] the list of plugins
     */
    protected function getPlugins(DeliveryExecution $deliveryExecution)
    {
        $delivery = $deliveryExecution->getDelivery();
        $serviceManager = ServiceManager::getServiceManager();

        $pluginService = $serviceManager->get(TestPluginService::CONFIG_ID);
        $testRunnerFeatureService = $serviceManager->get(TestRunnerFeatureService::SERVICE_ID);

        $allPlugins = $pluginService->getAllPlugins();

        $allTestRunnerFeatures = $testRunnerFeatureService->getAll();
        $activeTestRunnerFeaturesIds = explode(
            ',',
            $delivery->getOnePropertyValue(new \core_kernel_classes_Property(TestRunnerFeatures::TEST_RUNNER_FEATURES_PROPERTY))
        );

        // If test runner features are defined, we check if we need to disable some plugins accordingly
        if (count($allTestRunnerFeatures) > 0) {
            $pluginsToDisable = [];
            foreach ($allTestRunnerFeatures as $feature) {
                if (!in_array($feature->getId(), $activeTestRunnerFeaturesIds)) {
                    $pluginsToDisable = array_merge($pluginsToDisable, $feature->getPluginsIds());
                }
            }

            foreach ($allPlugins as $plugin) {
                if (!is_null($plugin) && in_array($plugin->getId(), $pluginsToDisable)) {
                    $plugin->setActive(false);
                }
            }
        }

        // return the list of active plugins
        return array_filter($allPlugins, function ($plugin) {
            return !is_null($plugin) && $plugin->isActive();
        });
    }

    /**
     * Get the container bootstrap
     * @param DeliveryExecution $deliveryExecution
     * @return string the bootstrap
     */
    protected function getBootstrap(DeliveryExecution $deliveryExecution)
    {
        //FIXME this config is misplaced, this should be a delivery property
        $config = ExtensionsManager::singleton()->getExtensionById('taoQtiTest')->getConfig('testRunner');
        return $config['bootstrap'];
    }
}
