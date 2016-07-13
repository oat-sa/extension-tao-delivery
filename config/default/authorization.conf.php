<?php
use oat\taoDelivery\model\authorization\strategy\AuthorizationAggregator;

return new AuthorizationAggregator([
    AuthorizationAggregator::OPTION_PROVIDERS => array()
]);