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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *  
 */

/**
 * Compiles a delivery, test and item
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_CompilationService extends tao_models_classes_Service
{
	
	/**
     * Compiles a delivery and everything it contains
     * 
     * @param core_kernel_classes_Resource $delivery
     * @return common_report_Report
     */
    public function compileDelivery( core_kernel_classes_Resource $delivery)
    {
        $report = new common_report_Report();
		$deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
		$compiledFolder = $deliveryService->getCompiledFolder($delivery);
		if(is_dir($compiledFolder)){
			tao_helpers_File::remove($compiledFolder, true);
		}
		if (!mkdir($compiledFolder)) {
			common_Logger::w('Could not create delivery directory \''.$compiledFolder.'\'');
		};
		foreach ($deliveryService->getRelatedTests($delivery) as $test) {
        	$testFolder = $compiledFolder. substr($test->getUri(), strpos($test->getUri(), '#') + 1). DIRECTORY_SEPARATOR;
			if(!is_dir($testFolder)){
				if (!mkdir($testFolder)) {
					common_Logger::w('Could not create test directory \''.$testFolder.'\'');
				}
			}
            $resultArray = $this->compileTest($test, $testFolder);
			$testReport = new common_report_Report();
            if ($resultArray["success"] == 1) {
                $testReport->add(new common_report_SuccessElement(__('Successfully compiled %1', $test->getLabel()), $test));
            } else {
                $testReport->add(new common_report_ErrorElement(__('Error while compiling %1', $test->getLabel())));
            }
	        $report->add($testReport);
        }
		return $report;
    }
	
	 public function compileTest(core_kernel_classes_Resource $test, $destination) {

		common_Logger::i('Compiling test '.$test->getLabel());
        $resultArray = array(
			'success' => 0,
			'failed' => array()
		);

		$testService = taoTests_models_classes_TestsService::singleton();
		$itemService = taoItems_models_classes_ItemsService::singleton();
		$items = $testService->getTestItems($test);

        // We will compile the item in any available language.

		foreach($items as $item){
			//check if the item exists: if not, append to the test failure message
			$itemFolder = $destination. substr($item->getUri(), strpos($item->getUri(), '#') + 1). DIRECTORY_SEPARATOR;
			if(!is_dir($itemFolder)){
				if (!mkdir($itemFolder)) {
					common_Logger::w('Could not create item directory \''.$itemFolder.'\'');
				}
			}
			if($itemService->isItemModelDefined($item)){
				try{
					$this->compileItem($item, $itemFolder);
				}
				catch(Exception $e){
					$resultArray["failed"]["errorMsg"][] = $e->getMessage();
				}
			}else{
				//the item no longer exists, set error message and break the loop and thus the compilation:
				if(!isset($resultArray["failed"]['unexistingItems'])){
					$resultArray["failed"]['unexistingItems'] = array();
				}
				$resultArray["failed"]['unexistingItems'][$item->getUri()] = $item;
				continue;
			}
		}
		
		if(empty($resultArray["failed"])){
			$resultArray["success"] = 1;
		}

        return (array) $resultArray;
	 }
	 
	 
	 public function compileItem($item, $destination) {
	 	$langs = $item->getUsedLanguages(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
		$compilationResult = array();
		foreach ($langs as $compilationLanguage) {
			$compiledFolder = $destination. $compilationLanguage. DIRECTORY_SEPARATOR;
			if(!is_dir($compiledFolder)){
				mkdir($compiledFolder);
			}
	    	$itemService = taoItems_models_classes_ItemsService::singleton();
			$itemService->deployItem($item, $compilationLanguage, $compiledFolder);
			//$compilationResult[] = $this->deployItem($item, $compilationLanguage, $compiledFolder);
		}
		return $compilationResult;
	 }
	 
    /**
     * finalises the compilation of a delivery
     * assumes that all the tests within are already compiled
     * 
     * @param core_kernel_classes_Resource $delivery
     * @return common_report_Report
     */
    public function finalizeDeliveryCompilation( core_kernel_classes_Resource $delivery)
    {
    	$service = taoDelivery_models_classes_DeliveryService::singleton();
        $generationResult = $service->generateProcess($delivery);
		
        // success
		if($generationResult['success']){
		    $report = new common_report_Report();
			$propCompiled = new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP);
			$delivery->editPropertyValues($propCompiled, GENERIS_TRUE);
			
		    if ($service->containsHumanAssistedMeasurements($delivery)) {
				$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CODINGMETHOD_PROP), TAO_DELIVERY_CODINGMETHOD_MANUAL);
				$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CODINGSTATUS_PROP), TAO_DELIVERY_CODINGSTATUS_GRADING);
			} else {
				$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CODINGMETHOD_PROP), TAO_DELIVERY_CODINGMETHOD_AUTOMATED);
			}

			$report->add(new common_report_SuccessElement(''));
			
        // failure
		}else{
		    $report = new common_report_Report();
			if(isset($generationResult['errors']['delivery'])){
				//bad design in delivery:
			    $error = array(
					'initialActivity' => $generationResult['errors']['delivery']['initialActivity'],
					'isolatedConnectors' => array()
				);
				foreach($generationResult['errors']['delivery']['isolatedConnectors'] as $connector){
					$error['isolatedConnectors'][] = $connector->getLabel();
				}
				$report->add(new taoDelivery_models_classes_CompilationErrorStructure(
				    taoDelivery_models_classes_CompilationErrorStructure::DELIVERY_ERROR_TYPE, $error));
			}elseif(isset($generationResult['errors']['tests'])){
				foreach($generationResult['errors']['tests'] as $testErrors){
				    
					//bad design in some tests:
					$connectors = array();
					foreach($testErrors['isolatedConnectors'] as $connector){
						$connectors[] = $connector->getLabel();
					}
					$error = array(
						'initialActivity' => $testErrors['initialActivity'],
						'label' => $testErrors['resource']->getLabel(),
						'isolatedConnectors' => $connectors
					);
				    $report->add(new taoDelivery_models_classes_CompilationErrorStructure(
				        taoDelivery_models_classes_CompilationErrorStructure::TEST_ERROR_TYPE, $error));
				}
			} else {
			    $report->add(new common_report_Report(''));
			}
		}
		return $report;
    }

}