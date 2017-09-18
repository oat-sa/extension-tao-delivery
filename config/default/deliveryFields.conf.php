<?php
/**
 * The default DeliveryFields service
 *
 * It uses for configuring properties and roles which have access
 */

use oat\tao\model\TaoOntology;

return new oat\taoDelivery\model\fields\DeliveryFieldsService([
    /**
     * Array with roles which have access for specific property
     * @type array
     */
    oat\taoDelivery\model\fields\DeliveryFieldsService::PROPERTY_CUSTOM_LABEL => [
		TaoOntology::PROPERTY_INSTANCE_ROLE_DELIVERY
    ]
]);