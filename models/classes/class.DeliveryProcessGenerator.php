<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery\models\classes\class.DeliveryProcessGenerator.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 03.01.2011, 18:01:25 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfEngine_models_classes_ProcessCloner
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('wfEngine/models/classes/class.ProcessCloner.php');

/* user defined includes */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007177-includes begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007177-includes end

/* user defined constants */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007177-constants begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007177-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryProcessGenerator
    extends wfEngine_models_classes_ProcessCloner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processError
     *
     * @access protected
     * @var array
     */
    protected $processError = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function __construct()
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000717D begin
		parent::__construct();
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000717D end
    }

    /**
     * Short description of method generateDeliveryProcess
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource delivery
     * @return core_kernel_classes_Resource
     */
    public function generateDeliveryProcess( core_kernel_classes_Resource $delivery)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007180 begin
		$failed = false;
		
		
		$this->processError = array('tests'=>array());
		$this->initCloningVariables();
		// $this->setCloneLabel("__Clone1");
		
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//check delivery process:
		$deliveryProcessChecker = new wfEngine_models_classes_ProcessChecker($process);
		if(!$deliveryProcessChecker->checkProcess(array('hasInitialActivity', 'hasNoIsolatedConnector'))){
			$this->processError['delivery'] = array(
				'resource' => $delivery,
				'initialActivity' => (bool) count($deliveryProcessChecker->getInitialActivities()),
				'isolatedConnectors' => $deliveryProcessChecker->getIsolatedConnectors()
			);
			return $returnValue;
		}
		
		$deliveryProcess = null;
		$deliveryProcess = $this->cloneWfResource(
			$process, 
			new core_kernel_classes_Class(CLASS_PROCESS), 
			array(PROPERTY_PROCESS_ACTIVITIES, PROPERTY_PROCESS_DIAGRAMDATA),
			__('Actual').' '.$process->getLabel()
		);
		
		if(!is_null($deliveryProcess)){
			
			//get all activity processes and clone them:
			$activities = $this->authoringService->getActivitiesByProcess($process);
			
			$deliveryAuthoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
			
			foreach($activities as $activityUri => $activity){
				
				$testProcess = $deliveryAuthoringService->getTestProcessFromActivity($activity);
				
				if(!is_null($testProcess)){
					//validate the test process:
					$processChecker = new wfEngine_models_classes_ProcessChecker($testProcess);
					
					if($processChecker->checkProcess(array('hasInitialActivity', 'hasNoIsolatedConnector'))){
						
						//clone the process segment:
						$testInterfaces = $this->cloneProcessSegment($testProcess, false);
						// print_r($testInterfaces);
						
						if(!empty($testInterfaces['in']) && !empty($testInterfaces['out'])){
							$inActivity = $testInterfaces['in'];
							$outActivities = $testInterfaces['out'];
							$this->addClonedActivity($inActivity, $activity, $outActivities);
						}else{
							throw new Exception("the process segment of the test process {$testProcess->uriResource} cannot be cloned");
						}
					}else{
						
						//log error:
						$failed = true;
						
						$testCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(TEST_TESTCONTENT_PROP, $testProcess->uriResource); 
						if(!$testCollection->isEmpty()){
							$test = $testCollection->get(0);
							$this->processError['tests'][$test->uriResource] = array(
								'resource' => $test,
								'initialActivity' => (bool) count($processChecker->getInitialActivities()),
								'isolatedConnectors' => $processChecker->getIsolatedConnectors()
							);
						}else{
							throw new Exception('no test found for the related test process');
						}
						
					}
				}else{
					$activityClone = $this->cloneActivity($activity);
					if(is_null($activityClone)){
						throw new Exception("the activity '{$activity->getLabel()}'({$activity->uriResource}) cannot be cloned");
					}else{
						$this->addClonedActivity($activityClone, $activity);
					}
				}
			}
			
			if($failed){
				
				//cancel everything
				$this->revertCloning();
				$this->authoringService->deleteProcess($deliveryProcess);
				$deliveryProcess = null;
				
			}else{
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
				
				//set the valid delivery process as the return value:
				$returnValue = $deliveryProcess;
			}
			
			
			// var_dump('end', $this);
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007180 end

        return $returnValue;
    }

    /**
     * Short description of method getErrors
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return array
     */
    public function getErrors()
    {
        $returnValue = array();

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007183 begin
		$returnValue = $this->processError;
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007183 end

        return (array) $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryProcessGenerator */

?>