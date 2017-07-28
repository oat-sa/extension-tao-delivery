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

namespace oat\taoDelivery\models\classes\feedback;

use oat\oatbox\service\ConfigurableService;
use oat\taoQtiTest\models\TestSessionService;
use qtism\runtime\tests\AssessmentTestSession;

abstract class StudentFeedbackService extends ConfigurableService
{

    const SERVICE_ID = 'taoDelivery/studentFeedback';

    /** @var \core_kernel_classes_Resource  */
    protected $delivery = null;

    /** @var AssessmentTestSession  */
    protected $session = null;

    /** @var array */
    protected $categories = [];

    public function getFeedbackData($deliveryExecutionUri)
    {

        /** @var \taoDelivery_models_classes_execution_Service $deliveryExecutionService */
        $deliveryExecutionService = $this->getServiceManager()->get(\taoDelivery_models_classes_execution_ServiceProxy::SERVICE_ID);

        $deliveryExecution = $deliveryExecutionService->getDeliveryExecution($deliveryExecutionUri);

        /** @var TestSessionService $testSessionService */
        $testSessionService = $this->getServiceManager()->get(TestSessionService::SERVICE_ID);
        $this->session = $testSessionService->getTestSession($deliveryExecution);


        $this->delivery = $deliveryExecution->getDelivery();

        $title = $this->getTitle();
        $description = $this->getDescription();
        $score = $this->getScore();
        $categories = $this->getCategories();
        $thresholds = $this->getThresholds();
        $range = $this->getRange();

        return new StudentFeedbackPayload($title, $description, $categories, $thresholds, $range, $score);
    }

    /**
     * return the title to display in the feedback
     * @return string
     */
    abstract protected function getTitle();

    /**
     * return the description to display in the feedback
     * @return string
     */
    abstract protected function getDescription();

    /**
     * return the categories to construct the feedback graph
     * @return array ["category1Key" => "My Category label"]
     */
    abstract protected function getCategories();


    /**
     * return the thresholds to construct the feedback graph
     * @return array ["Good knowledge" => 30]
     */
    abstract protected function getThresholds();

    /**
     * return the range to construct the feedback graph
     * @return array [0,100]
     */
    abstract protected function getRange();

    /**
     * return the score of a student
     * @return array ["category1Key" => ["value" => 1, "error" => 0.2], "category2Key" => ["value" => 3, "error" => 0.1]]
     */
    abstract protected function getScore();


}