<?php

use oat\taoDelivery\model\execution\DeliveryServerService;
use oat\taoDelivery\model\execution\implementation\ResultServerServiceFactory;

return new DeliveryServerService([
    DeliveryServerService::OPTION_RESULT_SERVER_SERVICE_FACTORY => new ResultServerServiceFactory()
]);
