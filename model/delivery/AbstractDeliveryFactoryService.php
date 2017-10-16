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
 * Copyright (c) 2017  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoDelivery\model\delivery;


use oat\oatbox\event\EventManager;
use oat\oatbox\service\ConfigurableService;
use oat\tao\helpers\form\ValidationRuleRegistry;
use oat\taoDelivery\model\container\delivery\ContainerProvider;
use oat\taoDeliveryRdf\model\ContainerRuntime;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;
use oat\taoDeliveryRdf\model\event\DeliveryCreatedEvent;
use oat\taoDeliveryRdf\model\TrackedStorage;

abstract class AbstractDeliveryFactoryService extends ConfigurableService implements DeliveryFactoryServiceInterface
{

    /**
     * @var DeliveryServiceInterface
     */
    private $deliveryService;

    /**
     * @var DeliveryInterface
     */
    private $delivery;

    protected function getDeliveryService()
    {
        if (!isset($this->deliveryService)) {
            $this->deliveryService = $this->getServiceManager()->get(DeliveryServiceInterface::SERVICE_ID);
        }

        return $this->deliveryService;
    }

    /**
     * Creates a new simple delivery
     *
     * @param \core_kernel_classes_Class $deliveryClass
     * @param \core_kernel_classes_Resource $test
     * @param string $label
     * @param DeliveryInterface $delivery
     * @return \common_report_Report
     */
    public function create(\core_kernel_classes_Class $deliveryClass, \core_kernel_classes_Resource $test, $label = '', DeliveryInterface $delivery = null) {

        \common_Logger::i('Creating '.$label.' with '.$test->getLabel().' under '.$deliveryClass->getLabel());

        $checkPropertiesReport = $this->checkTestProperties($test);
        if ($checkPropertiesReport->containsError()) {
            return $checkPropertiesReport;
        }

        if (!$delivery) {
            $delivery = $this->getDeliveryService()->createDelivery($deliveryClass, $label);
            // todo move to delivery service rdf $delivery = \core_kernel_classes_ResourceFactory::create($deliveryClass, $label);
        }

        $this->delivery = $delivery;

        $storage = new TrackedStorage();

        $testCompilerClass = \taoTests_models_classes_TestsService::singleton()->getCompilerClass($test);
        $compiler = new $testCompilerClass($test, $storage);

        $report = $compiler->compile();
        if ($report->getType() == \common_report_Report::TYPE_SUCCESS) {
            $serviceCall = $report->getData();

            $properties = array(
                RDFS_LABEL => $label,
                DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_DIRECTORY => $storage->getSpawnedDirectoryIds(),
                DeliveryAssemblyService::PROPERTY_ORIGIN => $test,
            );

            foreach ($this->getOption(self::OPTION_PROPERTIES) as $deliveryProperty => $testProperty) {
                $properties[$deliveryProperty] = $test->getPropertyValues(new \core_kernel_classes_Property($testProperty));
            }

            $container = null;
            if ($compiler instanceof ContainerProvider) {
                $container = $compiler->getContainer();
            }

            $compilationInstance = $this->storeDelivery($deliveryClass, $serviceCall, $container, $properties);
            $report->setData($compilationInstance);
        }

        return $report;
    }

    /**
     * @param \tao_models_classes_service_ServiceCall $serviceCall
     * @param string $container
     * @param array $properties
     * @return DeliveryInterface
     */
    protected function storeDelivery(\tao_models_classes_service_ServiceCall $serviceCall, $container, $properties = [])
    {
        $properties[DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_TIME] = time();
        $properties[DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_RUNTIME] = $serviceCall->toOntology();

        if (!isset($properties[DeliveryInterface::PROPERTY_RESULT_SERVER])) {
            $properties[DeliveryInterface::PROPERTY_RESULT_SERVER] = \taoResultServer_models_classes_ResultServerAuthoringService::singleton()->getDefaultResultServer();
        }

        if (!is_null($container)) {
            $properties[ContainerRuntime::PROPERTY_CONTAINER] = json_encode($container);
        }

        if ($this->delivery instanceof DeliveryInterface) {
            $this->delivery->setParameters($properties);
        }

        /** @var EventManager $eventManager */
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->trigger(new DeliveryCreatedEvent($this->delivery));

        return $this->delivery;
    }

    /**
     * @param \core_kernel_classes_Resource $test
     * @return \common_report_Report
     */
    private function checkTestProperties(\core_kernel_classes_Resource $test)
    {
        $report = \common_report_Report::createInfo(__('Test properties'));
        foreach ($this->getOption(self::OPTION_PROPERTIES) as $deliveryProperty => $testProperty) {

            $testPropertyInstance = new \core_kernel_classes_Property($testProperty);
            $validationValue = (string) $testPropertyInstance->getOnePropertyValue(new \core_kernel_classes_Property(ValidationRuleRegistry::PROPERTY_VALIDATION_RULE));
            $propertyValues = $test->getPropertyValues($testPropertyInstance);

            if ($validationValue == 'notEmpty' && empty($propertyValues)) {
                $report->add(\common_report_Report::TYPE_ERROR,
                    \common_report_Report::createFailure(__('Test publishing failed because "%s" is empty.', $testPropertyInstance->getLabel())));
            }
        }

        return $report;
    }
}
