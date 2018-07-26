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

use common_report_Report;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\execution\ServiceProxy;

class DeliveryExecutionDeleteService extends ConfigurableService
{
    const SERVICE_ID = 'taoDelivery/DeliveryExecutionDelete';

    const OPTION_DELETE_DELIVERY_EXECUTION_DATA_SERVICES = 'deleteDeliveryExecutionDataServices';

    /** @var common_report_Report  */
    private $report;

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
     * @return bool
     * @throws \Exception
     */
    public function execute(DeliveryExecutionDeleteRequest $request)
    {
        $this->report = common_report_Report::createInfo('Deleting Delivery Execution: '. $request->getDeliveryExecution()->getIdentifier());

        $shouldDelete = $this->deleteDeliveryExecutionData($request);

        if ($shouldDelete) {
            /** @var Service $executionService */
            $executionService = $this->getServiceLocator()->get(ServiceProxy::SERVICE_ID);
            // at the end delete the delivery execution itself.
            $deleted = $executionService->deleteDeliveryExecutionData($request);

            if ($deleted){
                $this->report->add(common_report_Report::createSuccess('Delivery Execution has been deleted.', $request->getDeliveryExecution()->getIdentifier()));
            } else {
                $this->report->add(common_report_Report::createInfo('Delivery Execution has NOT been deleted. DE id: '. $request->getDeliveryExecution()->getIdentifier()));
            }

            return $deleted;
        }

        return false;
    }

    /**
     * @return common_report_Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param DeliveryExecutionDeleteRequest $request
     * @return bool
     * @throws \Exception
     */
    protected function deleteDeliveryExecutionData(DeliveryExecutionDeleteRequest $request)
    {
        $services = $this->getDeliveryExecutionDeleteService();

        foreach ($services as $service) {
            try {
                $deleted = $service->deleteDeliveryExecutionData($request);
                if ($deleted) {
                    $this->report->add(common_report_Report::createSuccess(
                        'Delete execution Service: '. get_class($service) .' data has been deleted.',
                        $request->getDeliveryExecution()->getIdentifier())
                    );
                } else {
                    $this->report->add(common_report_Report::createInfo(
                        'Delete execution Service: '. get_class($service) .' data has nothing to delete'
                    ));
                }
            }catch (\Exception $exception){
                $this->report->add(common_report_Report::createFailure($exception->getMessage()));
            }
        }

        return true;
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