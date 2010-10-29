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

	public function generateDeliveryProcess(core_kernel_classes_Resource $process){
		
		$deliveryProcess = null;
		
		$deliveryProcess = $this->cloneWfResource($process, new core_kernel_classes_Class(CLASS_PROCESS), array(PROPERTY_PROCESS_ACTIVITIES, PROPERTY_PROCESS_DIAGRAMDATA));
		
		if(!is_null($deliveryProcess)){
			//get all activity processes and clone them:
			$activities = $this->authoringService->getActivitiesByProcess($process);
			foreach($activities as $activityUri => $activity){
			
				$testProcess = $this->getTestProcessFromActivity($activity);
				if(!is_null($testProcess)){
					//clone the process segment:
					$testInterfaces = $processCloner->cloneProcessSegment($testProcess);
					$this->addClonedActivity($activity, $testInterfaces['in'], $testInterfaces['out']);
					// $this->clonedActivities[$activity->uriResource] = $testInterfaces;
				}else{
					$activityClone = $this->cloneActivity($activity);
				}
			
				
				if(!is_null($activityClone)){
					$this->addClonedActivity($activity, $activityClone);
					// $this->clonedActivities[$activity->uriResource] = $activityClone->uriResource;
					$deliveryProcess->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activityClone->uriResource);
				}else{
					throw new Exception("the activity '{$activity->getLabel()}'({$activity->uriResource}) cannot be cloned");
				}
			}
			
			//reloop for connectors this time:
			foreach($activities as $activityUri => $activity){
				$this->currentActivity = $activity;
				$connectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
				foreach($connectors['next'] as $connector){
					$this->cloneConnector($connector);
				}
			}
		}
		
		return $deliveryProcess;
	}	
	
	public function getTestProcessFromActivity(core_kernel_classes_Resource $activity){
	
		$testProcess = null;
		
		foreach($this->getServicesByActivity($activity) as $service){
			$serviceDefinition = $service->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
			$serviceUrl = $serviceDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_URL));
			var_dump('$serviceUrl', $serviceUrl);
			if(!empty($serviceUrl)){
				$alledgedProcess = new core_kernel_classes_Resource($serviceUrl);
				if(wfEngine_helpers_ProcessUtil::checkType($alledgedProcess, new core_kernel_classes_Class(CLASS_PROCESS))){
					$testProcess = $alledgedProcess;
				}
			}
		}
		
		return $testProcess;
	}
	
} /* end of class taoDelivery_models_classes_DeliveryAuthoringService */	