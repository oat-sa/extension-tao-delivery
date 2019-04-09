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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoDelivery\controller;
use common_exception_BadRequest;
use common_exception_MissingParameter;
use common_exception_Unauthorized;
use common_Logger;
use oat\taoDelivery\model\execution\DeliveryExecutionService;

class DeliveryExecution extends \tao_actions_RestController
{
    public function state(){
        $service = $this->getDeliveryExecutionsService();
        try {
            if (!$this->isRequestGet()) {
                throw new \common_exception_BadRequest(sprintf('Bad Request Method: %s.', $this->getRequestMethod()));
            }
            $deliveryExecutionId = $this->getRequiredParameter('deliveryExecution');
            $scoreReport = null;
            if(!$service->getDeliveryExecution($deliveryExecutionId)->exists() || !$service->getDeliveryExecution($deliveryExecutionId)->getDelivery()->exists()){
               throw  new common_exception_BadRequest('');
            }
            $scores = $service->getScores($deliveryExecutionId);
            $state = $service->getState($deliveryExecutionId);
            if ($state === DeliveryExecutionService::TEST_STATUS_FINISHED) {
                $scoreReport = $service->getScoreReport($deliveryExecutionId, $scores);
            }
            return $this->returnJson([
                'success' => true,
                'state' => $state,
                'scoreReport' => $scoreReport,
                'scores' => $scores,
            ]);
        }catch (common_exception_MissingParameter $e) {
            return $this->generateError(false, 3, 'Bad request.');
        }
        /** @noinspection BadExceptionsProcessingInspection */
        catch (common_exception_BadRequest $e) {
            return $this->generateError(false, 2, 'Bad request.');
        } /** @noinspection PhpWrongCatchClausesOrderInspection, BadExceptionsProcessingInspection */
         /** @noinspection BadExceptionsProcessingInspection */
        catch (common_exception_Unauthorized $e) {
            return $this->generateError(false, 4, 'Unauthorized');

        } catch (Exception $e) {
            common_Logger::e('Failed to get delivery execution state: ' . $e->getMessage());
            return $this->generateError(false, 5, 'Failed to get delivery execution state.');
        }
    }

    /**
     * Validates request parameters.
     *
     * @param  string $requiredParameterName
     *
     * @return mixed
     *
     * @throws \common_exception_MissingParameter
     */
    protected function getRequiredParameter($requiredParameterName){
        $parameters = $this->getRequestParameters();
        if (array_key_exists($requiredParameterName, $parameters)) {
            $value = $parameters[$requiredParameterName];
            if (!empty($value)) {
                return $value;
            }
        }
        \common_Logger::i('Missing parameter ' . $requiredParameterName);
        throw new common_exception_MissingParameter('Missing parameter: ' . $requiredParameterName);
    }

    protected function getDeliveryExecutionsService(){
        return $this->getServiceLocator()->get(DeliveryExecutionService::SERVICE_ID);
    }

    protected function generateError($success, $errorCode, $errorMsg){
        return $this->returnJson([
            'success' => $success,
            'errorCode' => $errorCode,
            'errorMsg' => $errorMsg
        ]);
    }
}
