<?php

/**
 * Set to infinit by default
 */

use oat\taoDelivery\model\Capacity\DummyCapacityService;

return new DummyCapacityService([
    DummyCapacityService::OPTION_CAPACITY => -1
]);
