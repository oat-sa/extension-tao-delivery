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
 *
 */
namespace oat\taoDelivery\model\requirements;

use oat\oatbox\service\ConfigurableService;
use oat\taoAct\model\os\OSService;
use oat\taoAct\model\webbrowser\WebBrowserService;
use oat\taoDelivery\model\execution\DeliveryExecution;

class RequirementsService extends ConfigurableService implements RequirementsServiceInterface
{

    const PROPERTY_DELIVERY_APPROVED_BROWSER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ApprovedBrowser';
    const PROPERTY_DELIVERY_RESTRICT_BROWSER_USAGE = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#RestrictBrowserUsage';

    const PROPERTY_DELIVERY_APPROVED_OS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ApprovedOS';
    const PROPERTY_DELIVERY_RESTRICT_OS_USAGE = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#RestrictOSUsage';

    /**
     * Whether client complies delivery
     * @param DeliveryExecution $execution
     * @return boolean
     */
    public function isDeliveryComplies(DeliveryExecution $execution)
    {
        \common_Logger::i('Checking execution ' . $execution->getIdentifier() . 'comply.');

        $delivery = $execution->getDelivery();
        $isBrowserApproved = true;
        $isOSApproved = true;

        $isBrowserRestricted = $delivery->getUniquePropertyValue(new \core_kernel_classes_Property(self::PROPERTY_DELIVERY_RESTRICT_BROWSER_USAGE));
        if (INSTANCE_BOOLEAN_TRUE == $isBrowserRestricted->getUri()) {
            //@TODO property caching  - anyway we are operating with complied
            $browsers = $delivery->getPropertyValuesCollection(new \core_kernel_classes_Property(self::PROPERTY_DELIVERY_APPROVED_BROWSER));
            $isBrowserApproved = $this->complies($browsers->toArray(), WebBrowserService::class);
        }


        $isOSRestricted = $delivery->getUniquePropertyValue(new \core_kernel_classes_Property(self::PROPERTY_DELIVERY_RESTRICT_OS_USAGE));
        if (INSTANCE_BOOLEAN_TRUE == $isOSRestricted->getUri()) {
            //@TODO property caching  - anyway we are operating with complied
            $OS = $delivery->getPropertyValuesCollection(new \core_kernel_classes_Property(self::PROPERTY_DELIVERY_APPROVED_OS));
            $isOSApproved = $this->complies($OS->toArray(), OSService::class);
        }

        return $isBrowserApproved && $isOSApproved;
    }

    /**
     * @param array $conditions
     * @param string $conditionService
     * @return bool
     */
    protected function complies(array $conditions, $conditionService)
    {
        \common_Logger::i('Detected client: ' . $conditionService::singleton()->getClientName() . '@' . $conditionService::singleton()->getClientVersion());

        $result = false;
        /** @var \core_kernel_classes_Property $browser */
        foreach ($conditions as $condition) {
            $name = $condition->getOnePropertyValue(new \core_kernel_classes_Property($conditionService::PROPERTY_NAME));

            if ($conditionService::singleton()->getClientName() != $name) {
                continue;
            }

            $version = $condition->getOnePropertyValue(new \core_kernel_classes_Property($conditionService::PROPERTY_VERSION));
            if (-1 !== version_compare($conditionService::singleton()->getClientVersion(), $version)) {
                $result = true;
            }
        }

        return $result;
    }

}