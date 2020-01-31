<?php

use oat\taoDelivery\model\Capacity\DummyCapacityService;

/**
 * Set to infinit by default
 */

return new DummyCapacityService([
    DummyCapacityService::OPTION_CAPACITY => -1
]);
