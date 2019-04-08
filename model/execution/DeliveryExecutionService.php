<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 4/2/19
 * Time: 6:01 PM
 */

namespace oat\taoDelivery\model\execution;


use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\tao\helpers\Template;

use oat\taoOutcomeUi\helper\ResponseVariableFormatter;
use oat\taoOutcomeUi\model\ResultsService;
use oat\taoOutcomeUi\model\Wrapper\ResultServiceWrapper;
use Renderer;

class DeliveryExecutionService extends  ConfigurableService
{
    use OntologyAwareTrait;

    const SERVICE_ID = 'taoDelivery/deliveryExecutionService';

    const DELIVERY_EXECUTION_PARAM_DELIVERYEXECUTIONID  = 'deliveryExecutionID';
    const DELIVERY_EXECUTION_PARAM_TESTSESSIONID        = 'testSessionID';

    const TEST_STATUS_INPROGRESS                        = 'InProgress';
    const TEST_STATUS_TIMEOUT                           = 'Timeout';
    const TEST_STATUS_FINISHED                          = 'Finished';

    const DELIVERY_REPORT_LANGUAGE                      = 'http://www.tao.lu/Ontologies/BTDelivery.rdf#DeliveryReportLanguage';

    /**
     * Gets delivery execution state.
     *
     * @param  string $deliveryExecutionID
     *
     * @throws \Exception
     * @throws \common_Exception
     *
     * @return string Returns test delivery state as a string.
     */
    public function getState($deliveryExecutionID)
    {
        return $this->getDeliveryExecutionState($deliveryExecutionID);
    }

    /**
     * Gets score report per delivery execution.
     *
     * @param  string $deliveryExecutionID
     *
     * @throws \Exception
     * @throws \common_Exception
     * @throws \common_exception_Error
     *
     * @return array
     */
    public function getScores($deliveryExecutionID)
    {
        $executionService = ServiceProxy::singleton();
        $deliveryExecution = $executionService->getDeliveryExecution($deliveryExecutionID);

        $resultService = ResultsService::singleton();

        /** @var \oat\taoResultServer\models\classes\ResultManagement $implementation */
        $implementation = $resultService->getReadableImplementation($deliveryExecution->getDelivery());
        $resultService->setImplementation($implementation);

        $variables = $this->getResultVariables($deliveryExecution->getIdentifier());
        $scoreReport = $resultService->calculateResponseStatistics($variables);




        $testCallIds = $resultService->getTestsFromDeliveryResult($deliveryExecution->getIdentifier());
        foreach ($testCallIds as $testCallId) {
            $testVariables = $resultService->getVariablesFromObjectResult($testCallId);
            foreach ($testVariables as $testVariable) {
                /** @var \taoResultServer_models_classes_OutcomeVariable $variable */
                $variable= $testVariable[0]->variable;
                    $scoreReport[$variable->getIdentifier()] = $variable->getValue();
            }
        }
        $testCallIds = $resultService->getTestsFromDeliveryResult($deliveryExecution->getIdentifier());
        foreach ($testCallIds as $testCallId) {
            $testVariables = $resultService->getVariablesFromObjectResult($testCallId);
            foreach ($testVariables as $testVariable) {
                /** @var \taoResultServer_models_classes_OutcomeVariable $variable */
                $variable= $testVariable[0]->variable;
                if (in_array($variable->getIdentifier(), array_keys($scoreReport))) {
                    $scoreReport[$variable->getIdentifier()] = $variable->getValue();
                }
            }
        }

        return $scoreReport;
    }

    /**
     * Getting report for the delivery execution
     * @param $deliveryExecutionId
     * @param $scores
     * @return array|null|string
     * @throws \common_Exception
     * @throws \common_exception_Error
     * @throws \common_exception_NotFound
     * @throws \core_kernel_persistence_Exception
     */
    public function getScoreReport($deliveryExecutionId, $scores)
    {


        $lang = $this->getLang($deliveryExecutionId);
        switch ($lang) {
            case 'fr-CA':
                break;
            default:
                $lang = 'en-US';
        }
        $renderer = new Renderer();
        $template = Template::getTemplate('deliveryReport/' . $lang .'.tpl', 'taoDelivery');
        $renderer->setData('scores', $scores);

        $renderer->setTemplate($template);
        return $renderer->render();
    }



    /**
     * Gets delivery execution state.
     *
     * @param  string $deliveryExecutionUri
     *
     * @return string                       Returns delivery execution state as a string.
     *
     * @throws \common_Exception
     */
    private function getDeliveryExecutionState($deliveryExecutionUri)
    {
        $executionService = ServiceProxy::singleton();
        $deliveryExecution = $executionService->getDeliveryExecution($deliveryExecutionUri);

        $state = $deliveryExecution->getState()->getUri();

        return ($state !== DeliveryExecution::STATE_ACTIVE && $state !== DeliveryExecution::STATE_PAUSED)
            ? self::TEST_STATUS_FINISHED
            : self::TEST_STATUS_INPROGRESS;
    }

    /**
     * Getting language of the delivery byt the delivery execution
     * @param $deliveryExecutionId
     * @return string
     * @throws \common_exception_NotFound
     * @throws \core_kernel_persistence_Exception
     */
    public function getLang($deliveryExecutionId)
    {
        $executionService = ServiceProxy::singleton();
        $deliveryExecution = $executionService->getDeliveryExecution($deliveryExecutionId);
        $delivery = $deliveryExecution->getDelivery();
        /** @var \core_kernel_classes_Resource $reportLang */
        $reportLang = $delivery->getOnePropertyValue($this->getProperty(self::DELIVERY_REPORT_LANGUAGE));
        return $reportLang ? (string)$reportLang->getOnePropertyValue($this->getProperty(OntologyRdf::RDF_VALUE)) : '';
    }



    public function getDeliveryExecution($deliveryExecution){
         return ServiceProxy::singleton()->getDeliveryExecution($deliveryExecution);

    }

    public function getResultService(){
        return $this->getServiceManager()->get(ResultServiceWrapper::SERVICE_ID)->getService();
    }


    protected function getResultVariables($resultId, $filterSubmission='', $filterTypes = array()){
        $filterSubmission = ResultsService::VARIABLES_FILTER_LAST_SUBMITTED;
        $filterTypes = array(\taoResultServer_models_classes_ResponseVariable::class, \taoResultServer_models_classes_OutcomeVariable::class, \taoResultServer_models_classes_TraceVariable::class);

        $resultService = ResultsService::singleton();
        $variables = $resultService->getStructuredVariables($resultId, $filterSubmission, array_merge($filterTypes, [\taoResultServer_models_classes_ResponseVariable::class]));
        $displayedVariables = $resultService->filterStructuredVariables($variables, $filterTypes);
        $responses = ResponseVariableFormatter::formatStructuredVariablesToItemState($variables);
        $excludedVariables = array_flip(['numAttempts', 'duration']);

        foreach ($displayedVariables as &$item) {
            if (!isset($item['uri'])) {
                continue;
            }
            $itemUri = $item['uri'];
            $item['state'] = isset($responses[$itemUri][$item['attempt']])
                ? json_encode(array_diff_key($responses[$itemUri][$item['attempt']], $excludedVariables))
                : null;
        }

        return $displayedVariables;
    }






}
