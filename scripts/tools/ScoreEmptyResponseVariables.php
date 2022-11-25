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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoDelivery\scripts\tools;

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\extension\script\ScriptAction;
use oat\oatbox\reporting\Report;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\model\execution\DeliveryExecutionService;
use oat\taoDelivery\model\RuntimeService;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;
use oat\taoQtiTest\models\QtiTestUtils;
use oat\taoResultServer\models\classes\ResultServerService;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\data\AssessmentTest;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\data\state\OutcomeDeclaration;
use qtism\data\state\OutcomeDeclarationCollection;
use qtism\data\state\Value;
use qtism\data\state\ValueCollection;
use qtism\data\TestPart;
use tao_models_classes_service_ServiceCallHelper;
use taoResultServer_models_classes_OutcomeVariable as OutcomeVariable;
use taoResultServer_models_classes_ResponseVariable as ResponseVariable;

/**
 * Script for filling the empty scores for items in case delivery execution
 * terminated of finished by system because it was detected as a stale execution.
 *
 * Usage:
 * php index.php 'oat\taoDelivery\scripts\tools\ScoreEmptyResponseVariables'
 * -de[--deliveryExecutionIds] Delivery Execution Identifiers [Required. Comma separated if several]
 * -as[--allowedStatuses] Delivery Execution Statuses [Optional. Comma separated if several]
 * -wr[--wetRun] 1 [Optional. By default 0]
 */
final class ScoreEmptyResponseVariables extends ScriptAction
{
    use OntologyAwareTrait;

    public const OPTION_DELIVERY_EXECUTION_IDS = 'deliveryExecutionIds';
    public const OPTION_ALLOWED_STATUSES = 'allowedStatuses';
    public const OPTION_WET_RUN = 'wetRun';

    private const ALLOWED_STATUSES = [
        DeliveryExecutionInterface::STATE_FINISHED,
        DeliveryExecutionInterface::STATE_TERMINATED
    ];

    private static array $testDefinitionsByDeliveryId = [];
    private static array $mappedItemsByTestDefinition = [];

    protected function provideOptions(): array
    {
        return [
            self::OPTION_DELIVERY_EXECUTION_IDS => [
                'prefix' => 'de',
                'longPrefix' => self::OPTION_DELIVERY_EXECUTION_IDS,
                'required' => true,
                'description' => 'Delivery execution ids for which need to do score',
            ],
            self::OPTION_ALLOWED_STATUSES => [
                'prefix' => 'as',
                'longPrefix' => self::OPTION_ALLOWED_STATUSES,
                'required' => false,
                'description' => 'Override allowed statuses'
            ],
            self::OPTION_WET_RUN => [
                'prefix' => 'wr',
                'longPrefix' => self::OPTION_WET_RUN,
                'required' => false,
                'cast' => 'boolean',
                'description' => 'Bit of wet run value',
            ],
        ];
    }

    protected function provideDescription(): string
    {
        return 'Does scoring for the missed or empty items in specified delivery execution';
    }

    protected function run(): Report
    {
        $isWetRunFlag = $this->getOption(self::OPTION_WET_RUN);
        $deliveryExecutionIds = explode(',', $this->getOption(self::OPTION_DELIVERY_EXECUTION_IDS));

        $allowedStatuses = self::ALLOWED_STATUSES;
        if ($this->hasOption(self::OPTION_ALLOWED_STATUSES)) {
            $allowedStatuses = explode(',', $this->getOption(self::OPTION_ALLOWED_STATUSES));
        }

        $resultServer = $this->getResultServer();
        $resultStorage = $resultServer->getResultStorage();
        $variables = $this->createVariables();

        $report = Report::createInfo('Running scoring operation...');

        foreach ($deliveryExecutionIds as $id) {
            $subReport = Report::createInfo(sprintf('Processing delivery execution %s', $id));

            $deliveryExecution = $this->getServiceProxy()->getDeliveryExecution($id);

            if (!in_array($deliveryExecution->getState()->getUri(), $allowedStatuses, true)) {
                $subReport->add(Report::createWarning('Delivery execution has not allowed state, skipped'));
                $report->add($subReport);
                continue;
            }

            $resultVariables = $resultStorage->getDeliveryVariables($id);

            $existedTestItemVariables = $this->fetchUniqueItemsIdFromResponseVariables($resultVariables);
            $testOutcomeVariables = $this->filterTestOutcomeVariables($resultVariables);
            $testDefinition = $this->fetchTestDefinition($deliveryExecution);
            $assessmentItemHrefByItemId = $this->extractAssocAssessmentItemHrefByItemId($testDefinition);
            $filteredTestOutcomeVariables = $this->extractOutcomeVariables($testDefinition->getOutcomeDeclarations());

            $filteredTestOutcomeVariablesForInsert = $this->buildFilteredTestOutcomeVariablesSet(
                $filteredTestOutcomeVariables,
                $testOutcomeVariables
            );

            if ($isWetRunFlag) {
                $testResource = $deliveryExecution->getDelivery()->getOnePropertyValue(
                    $this->getProperty(DeliveryAssemblyService::PROPERTY_ORIGIN)
                );
                $resultStorage->storeTestVariables(
                    $id,
                    $testResource->getUri(),
                    $filteredTestOutcomeVariablesForInsert,
                    $id
                );
            }

            $scored = 0;

            foreach ($assessmentItemHrefByItemId as $itemId => $itemData) {
                if (in_array($itemId, $existedTestItemVariables, true)) {
                    continue;
                }
                if ($isWetRunFlag) {
                    [$itemUri, , $testUri] = explode('|', $itemData['href']);
                    $callItemId = sprintf('%s.%s.%s', $id, $itemId, 0);
                    $dynamicOutcomeVariables = [];
                    foreach ($itemData['outcomes'] as $outcomeIdentifier => $data) {
                        $dynamicOutcomeVariables[] = (new OutcomeVariable())
                            ->setIdentifier($outcomeIdentifier)
                            ->setValue($data['value'])
                            ->setCardinality($data['cardinality'])
                            ->setBaseType($data['baseType']);
                    }

                    $resultStorage->storeItemVariables(
                        $id,
                        $testUri,
                        $itemUri,
                        array_merge($variables, $dynamicOutcomeVariables),
                        $callItemId
                    );
                }
                $scored++;
            }

            $subReport->add(Report::createSuccess(sprintf(
                'Response item variables were scored for %s items, updated %s test outcome variables',
                $scored,
                count($filteredTestOutcomeVariablesForInsert)
            )));

            if ($isWetRunFlag) {
                $this->logInfo($subReport->getMessage());
            }

            $report->add($subReport);
        }

        return $report;
    }

    private function fetchTestDefinition(DeliveryExecution $deliveryExecution): AssessmentTest
    {
        $compiledDeliveryUri = $deliveryExecution->getDelivery()->getUri();

        if (!isset(self::$testDefinitionsByDeliveryId[$compiledDeliveryUri])) {
            $runtime = $this->getRuntimeService()->getRuntime($compiledDeliveryUri);
            $inputParameters = tao_models_classes_service_ServiceCallHelper::getInputValues($runtime, []);

            self::$testDefinitionsByDeliveryId[$compiledDeliveryUri] = $this->getQtiTestUtil()->getTestDefinition(
                $inputParameters['QtiTestCompilation']
            );
        }

        return self::$testDefinitionsByDeliveryId[$compiledDeliveryUri];
    }

    private function fetchUniqueItemsIdFromResponseVariables(array $resultVariables): array
    {
        //filter Item Variables
        $itemFilteredVariables = array_filter($resultVariables, static function (array $variable) {
            return $variable[0]->callIdItem !== null;
        });
        // map items id
        $items = array_map(static function (array $variable) {
            $exploded = explode('.', $variable[0]->callIdItem);
            $occurrence = array_pop($exploded);
            return array_pop($exploded);
        }, $itemFilteredVariables);

        return array_unique($items);
    }

    private function filterTestOutcomeVariables(array $resultVariables): array
    {
        // filter Test Variables
        $testFilteredVariables = array_filter($resultVariables, static function (array $variable) {
            return $variable[0]->callIdItem === null;
        });

        return array_map(function (array $record) {
            /** @var OutcomeVariable $variable */
            $variable = $record[0]->variable;
            return $variable->getIdentifier();
        }, $testFilteredVariables);
    }

    private function extractAssocAssessmentItemHrefByItemId(AssessmentTest $assessmentTest): array
    {
        if (isset(self::$mappedItemsByTestDefinition[$assessmentTest->getIdentifier()])) {
            return self::$mappedItemsByTestDefinition[$assessmentTest->getIdentifier()];
        }

        $result = [];
        /** @var TestPart $testPart */
        foreach ($assessmentTest->getTestParts() as $testPart) {
            foreach ($testPart->getAssessmentSections() as $assessmentSection) {
                /** @var ExtendedAssessmentItemRef $sectionPart */
                foreach ($assessmentSection->getSectionParts() as $sectionPart) {
                    $result[$sectionPart->getIdentifier()] = [
                        'href' => $sectionPart->getHref(),
                        'outcomes' => $this->extractOutcomeVariables($sectionPart->getOutcomeDeclarations())
                    ];
                }
            }
        }

        self::$mappedItemsByTestDefinition[$assessmentTest->getIdentifier()] = $result;

        return $result;
    }

    private function createVariables(): array
    {
        $numAttempts = (new ResponseVariable())
            ->setIdentifier('numAttempts')
            ->setCandidateResponse('1')
            ->setCardinality('single')
            ->setBaseType('integer');
        $duration = (new ResponseVariable())
            ->setIdentifier('duration')
            ->setCandidateResponse('PT0S')
            ->setCardinality('single')
            ->setBaseType('duration');
        $response = (new ResponseVariable())
            ->setIdentifier('RESPONSE')
            ->setCandidateResponse('')
            ->setCardinality('single')
            ->setBaseType('identifier');
        $completionsStatus = (new OutcomeVariable())
            ->setValue('completed')
            ->setIdentifier('completionStatus')
            ->setCardinality('single')
            ->setBaseType('identifier');

        return [$numAttempts, $duration, $response, $completionsStatus];
    }

    private function extractOutcomeVariables(OutcomeDeclarationCollection $outcomeDeclarationCollection): array
    {
        $outcomes = [];

        /** @var OutcomeDeclaration $outcomeDeclaration */
        foreach ($outcomeDeclarationCollection as $outcomeDeclaration) {
            $value = 0;

            if (
                $outcomeDeclaration->hasDefaultValue()
                && $outcomeDeclaration->getDefaultValue()->getValues() instanceof ValueCollection
            ) {
                /** @var Value $defaultValue */
                $defaultValue = $outcomeDeclaration->getDefaultValue()->getValues()[0];
                $value = $defaultValue->getValue();
            }
            $outcomes[$outcomeDeclaration->getIdentifier()] = [
                'baseType' => BaseType::getNameByConstant($outcomeDeclaration->getBaseType()),
                'cardinality' => Cardinality::getNameByConstant($outcomeDeclaration->getCardinality()),
                'value' => $value
            ];
        }

        return $outcomes;
    }

    private function buildFilteredTestOutcomeVariablesSet(
        array $testOutcomeVariablesByIdentifier,
        array $existedOutcomeVariables
    ): array {
        $resultSet = [];
        foreach ($testOutcomeVariablesByIdentifier as $identifier => $testOutcomeVariable) {
            if (in_array($identifier, $existedOutcomeVariables, true)) {
                continue;
            }
            $resultSet[] = (new OutcomeVariable())
                ->setIdentifier($identifier)
                ->setValue($testOutcomeVariable['value'])
                ->setBaseType($testOutcomeVariable['baseType'])
                ->setCardinality($testOutcomeVariable['cardinality']);
        }

        return $resultSet;
    }

    private function getServiceProxy(): DeliveryExecutionService
    {
        return $this->getServiceLocator()->get(DeliveryExecutionService::SERVICE_ID);
    }

    private function getQtiTestUtil(): QtiTestUtils
    {
        return $this->getServiceLocator()->get(QtiTestUtils::SERVICE_ID);
    }

    private function getResultServer(): ResultServerService
    {
        return $this->getServiceLocator()->get(ResultServerService::SERVICE_ID);
    }

    private function getRuntimeService(): RuntimeService
    {
        return $this->getServiceLocator()->get(RuntimeService::SERVICE_ID);
    }
}
