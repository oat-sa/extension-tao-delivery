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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoDelivery\controller;


use common_Logger;
use oat\taoDelivery\models\classes\feedback\StudentFeedbackService;
/**
 * StudentFeedback Controller
 *
 * @package taoDelivery
 */
class StudentFeedback extends \tao_actions_CommonModule
{

    public function index()
    {

        $this->setData('logout', _url('index', 'DeliveryServer', 'taoDelivery'));

    }


    public function getData()
    {
        if(!$this->hasRequestParameter('deliveryExecution')){
            common_Logger::w("Missing deliveryExecution parameter");
            $this->returnJson(['success' => false, 'message' => __('Your request is not well formed')], 422);
            return;
        }

        if(!$this->getServiceManager()->has(StudentFeedbackService::SERVICE_ID)){
            common_Logger::w("Student feedback service isn't set");
            $this->returnJson(['success' => false, 'message' => __('Something went wrong during you data retrieval')], 500);
            return ;
        }

        /** @var StudentFeedbackService $studentFeedbackService */
        $studentFeedbackService = $this->getServiceManager()->get(StudentFeedbackService::SERVICE_ID);
        $data = $studentFeedbackService->getFeedbackdata($this->getRequestParameter('deliveryExecution'));
        if(!$data){
            $this->returnJson(['success' => false, 'message' => __('Something went wrong during you data retrieval')], 500);
            return;
        }
        $this->returnJson($data, 200);
    }

}
