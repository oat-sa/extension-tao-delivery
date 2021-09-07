<?php

namespace oat\taoDelivery\scripts\install;

use common_Exception;
use oat\oatbox\extension\InstallAction;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\oatbox\service\ServiceNotFoundException;
use oat\tao\model\counter\CounterService;
use oat\tao\model\counter\CounterServiceException;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;

class RegisterCounters extends InstallAction
{
    public const COUNTER_SHORT_NAME_DELIVERY_EXECUTION_CREATED = 'taoDelivery:created';
    public const COUNTER_SHORT_NAME_DELIVERY_EXECUTION_STATE = 'taoDelivery:state';

    /**
     * @param array $params
     * @throws common_Exception
     * @throws ServiceNotFoundException
     * @throws InvalidServiceManagerException
     * @throws CounterServiceException
     */
    public function __invoke($params = [])
    {
        /** @var CounterService $counterService */
        $counterService = $this->getServiceManager()->get(CounterService::SERVICE_ID);

        // Attach to Delivery Execution Creation Event.
        $counterService->attach(
            DeliveryExecutionCreated::class,
            self::COUNTER_SHORT_NAME_DELIVERY_EXECUTION_CREATED
        );

        // Attach to Delivery Execution State Event.
        // The DeliveryExecutionState::getState() method is used to count depending on the
        // returned value.
        $counterService->attach(
            DeliveryExecutionState::class,
            self::COUNTER_SHORT_NAME_DELIVERY_EXECUTION_STATE,
            'getState'
        );

        $this->getServiceManager()->register(CounterService::SERVICE_ID, $counterService);
    }
}
