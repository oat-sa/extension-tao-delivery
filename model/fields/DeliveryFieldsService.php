<?php

namespace oat\taoDelivery\model\fields;

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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;

/**
 * Service to manage the custom fields of deliveries
 *
 * @access public
 * @author Aleksej Tikhanovich, <aleksej@taotesting.com>
 * @package taoDelivery
 */
class DeliveryFieldsService extends ConfigurableService
{
    use OntologyAwareTrait;

    const SERVICE_ID = 'taoDelivery/deliveryFields';

    const PROPERTY_CUSTOM_LABEL = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CustomLabel';

    /**
     * Getting custom label from Delivery
     *
     * @param \core_kernel_classes_Resource $delivery
     * @param string $label
     * @return string
     */
    public function getLabel(\core_kernel_classes_Resource $delivery, $label = '')
    {
        $user = \common_session_SessionManager::getSession()->getUser();
        $customLabelRoles = $this->getOption(self::PROPERTY_CUSTOM_LABEL);
        if (array_intersect($customLabelRoles, $user->getRoles())) {
            $property = $this->getProperty(self::PROPERTY_CUSTOM_LABEL);
            if ((string)$delivery->getOnePropertyValue($property)) {
                $label = $delivery->getOnePropertyValue($property);
            }
        }
        return (string) $label;
    }
}
