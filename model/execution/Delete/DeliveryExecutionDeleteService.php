<?php
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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoDelivery\model\execution\Delete;

use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\execution\Service;

class DeliveryExecutionDeleteService extends ConfigurableService
{
    const SERVICE_ID = 'taoDeliveryRdf/DeliveryExecutionDelete';

    const OPTION_DELETE_DELIVERY_EXECUTION_DATA_SERVICES = 'deleteDeliveryExecutionDataServices';

    /**
     * DeliveryExecutionDeleteService constructor.
     * @param array $options
     * @throws \common_exception_Error
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        if (!$this->hasOption(static::OPTION_DELETE_DELIVERY_EXECUTION_DATA_SERVICES)) {
            throw new \common_exception_Error('Invalid Option provided: ' . static::OPTION_DELETE_DELIVERY_EXECUTION_DATA_SERVICES);
        }
    }

    /**
     * @param DeliveryExecutionDeleteRequest $request
     * @throws \Exception
     */
    public function execute(DeliveryExecutionDeleteRequest $request)
    {
        $this->deleteDeliveryExecutionData($request);

        /** @var Service $executionService */
        $executionService = $this->getServiceLocator()->get('taoDelivery/execution_service');
        // at the end delete the delivery execution itself.
        $executionService->deleteDeliveryExecutionData($request);
    }

    /**
     * @param DeliveryExecutionDeleteRequest $request
     * @throws \Exception
     */
    protected function deleteDeliveryExecutionData(DeliveryExecutionDeleteRequest $request)
    {
        $services = $this->getDeliveryExecutionDeleteService();
        foreach ($services as $service) {
            $service->deleteDeliveryExecutionData($request);
        }
    }

    /**
     * @return DeliveryExecutionDelete[]
     * @throws \common_exception_Error
     */
    private function getDeliveryExecutionDeleteService()
    {
        $services = [];
        $servicesStrings = $this->getOption(static::OPTION_DELETE_DELIVERY_EXECUTION_DATA_SERVICES);
        foreach ($servicesStrings as $serviceString) {
            $deleteService = $this->getServiceLocator()->get($serviceString);
            if (!$deleteService instanceof DeliveryExecutionDelete) {
                throw new \common_exception_Error('Invalid Delete Service provided: ' . $serviceString);
            }

            $services[] = $deleteService;
        }

        return $services;
    }
}