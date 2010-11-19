<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}
require_once('wfEngine/models/classes/class.ProcessCloner.php');
/**
 * The taoDelivery_models_classes_DeliveryProcessGenerator class
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_DeliveryProcessGenerator
    extends wfEngine_models_classes_ProcessCloner
{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function generateDeliveryProcess(core_kernel_classes_Resource $delivery){
		
		$this->initCloningVariables();
		$this->setCloneLabel("__Clone3");
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		$deliveryProcess = null;
		$deliveryProcess = $this->cloneWfResource(
			$process, 
			new core_kernel_classes_Class(CLASS_PROCESS), 
			array(PROPERTY_PROCESS_ACTIVITIES, PROPERTY_PROCESS_DIAGRAMDATA),
			'Actual '.$process->getLabel()
		);
		
		if(!is_null($deliveryProcess)){
			//get all activity processes and clone them:
			$activities = $this->authoringService->getActivitiesByProcess($process);
			
			$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
			
			foreach($activities as $activityUri => $activity){
				
				$testProcess = $authoringService->getTestProcessFromActivity($activity);
				
				if(!is_null($testProcess)){
					//clone the process segment:
					$testInterfaces = $this->cloneProcessSegment($testProcess, false);
					// print_r($testInterfaces);
					
					if(!empty($testInterfaces['in']) && !empty($testInterfaces['out'])){
						$inActivityUri = $testInterfaces['in'];
						$outActivityUris = $testInterfaces['out'];
						$this->addClonedActivity($activity, $inActivityUri, $outActivityUris);
					}else{
						throw new Exception("the process segment of the test process {$testProcess->uriResource} cannot be cloned");
					}
				}else{
					// $activityClone = $this->cloneActivity($activity);
					if(is_null($this->cloneActivity($activity))){
						throw new Exception("the activity '{$activity->getLabel()}'({$activity->uriResource}) cannot be cloned");
					}
				}
			}
			
			//add all cloned activities to the cloned delivery process:
			foreach($this->getClonedActivities() as $activityClone){
				$deliveryProcess->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activityClone->uriResource);
			}
			
			//reloop for connectors this time:
			foreach($activities as $activityUri => $activity){
				$this->currentActivity = $activity;
				$connectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
				foreach($connectors['next'] as $connector){
					$this->cloneConnector($connector);
				}
			}
			
			// var_dump('end', $this);
		}
		
		return $deliveryProcess;
	}	
	
} /* end of class taoDelivery_models_classes_DeliveryAuthoringService */	