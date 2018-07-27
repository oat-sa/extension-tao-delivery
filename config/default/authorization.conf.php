<?php
use oat\taoDelivery\model\authorization\strategy\AuthorizationAggregator;
use oat\taoDelivery\model\authorization\strategy\StateValidation;

return new AuthorizationAggregator([
    AuthorizationAggregator::OPTION_PROVIDERS => array(
        new StateValidation()
    )
]);