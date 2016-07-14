<?php
use oat\taoDelivery\model\authorization\strategy\AuthorizationAggregator;
use oat\taoDelivery\model\authorization\strategy\StateValidation;
use oat\taoDelivery\model\execution\DeliveryExecution;

return new AuthorizationAggregator([
    AuthorizationAggregator::OPTION_PROVIDERS => array(
        new StateValidation()
    )
]);