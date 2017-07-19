<?php
/**
 * The default DeliveryFields service
 *
 * It uses for configuring properties and roles which have access
 */
return new oat\taoDelivery\model\fields\DeliveryFieldsService([
    /**
     * Array with roles which have access for specific property
     * @type array
     */
    oat\taoDelivery\model\fields\DeliveryFieldsService::PROPERTY_CUSTOM_LABEL => [
        INSTANCE_ROLE_DELIVERY
    ]
]);