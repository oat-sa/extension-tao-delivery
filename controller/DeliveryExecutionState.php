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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 * @author Sergei Mikhailov <sergei.mikhailov@taotesting.com>
 */
declare(strict_types=1);

namespace oat\taoDelivery\controller;

use common_exception_MissingParameter;
use common_exception_NotFound as NotFoundException;
use common_exception_NotImplemented as NotImplementedException;
use common_exception_RestApi as ApiException;
use oat\taoDelivery\model\execution\Service;
use oat\taoDelivery\model\execution\ServiceProxy;
use oat\taoDelivery\model\execution\StateServiceInterface;
use tao_actions_RestController as RestController;

/** Kindly use `funcAcl` in order to assign the roles, having access to the controller */
class DeliveryExecutionState extends RestController
{
    /**
     * @throws NotFoundException
     *
     * @throws NotImplementedException
     * @throws NotFoundException
     */
    public function index(): void
    {
        $queryParams = $this->getPsrRequest()->getQueryParams();

        switch ($this->getPsrRequest()->getMethod()) {
            case 'PUT':
                $this->put($queryParams['uri'] ?? '');
                break;
            default:
                /** @noinspection PhpUnhandledExceptionInspection */
                $this->returnFailure(new ApiException('Not implemented'));
        }
    }

    private function put(string $uri): void
    {
        if (empty($uri)) {
            $this->returnFailure(new common_exception_MissingParameter('uri'));
        }

        $state = $this->extractState();

        $deliveryExecution = $this->getExecutionService()->getDeliveryExecution($uri);

        try {
            if ($deliveryExecution->getState()->getUri() !== $state) {
                $deliveryExecution->setState($state);
            }
        } catch (NotFoundException $exception) {
            $this->returnFailure(new NotFoundException('Delivery execution not found', 404, $exception));
        }

        $this->returnSuccess([], false);
    }

    protected function getExecutionService(): Service
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(ServiceProxy::SERVICE_ID);
    }

    protected function getStateService(): StateServiceInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(StateServiceInterface::SERVICE_ID);
    }

    private function extractState(): string
    {
        $data = json_decode((string)$this->getPsrRequest()->getBody(), true);

        if (empty($data['value']) || !in_array($data['value'], $this->getStateService()->getDeliveriesStates(), true)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->returnFailure(new common_exception_MissingParameter('value'));
        }

        return $data['value'];
    }
}
