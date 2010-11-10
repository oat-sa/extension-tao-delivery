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
 * The taoDelivery_models_classes_DeliveryAuthoringService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_DeliveryAuthoringService
    extends wfEngine_models_classes_ProcessAuthoringService
{
   

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct()
    {
		parent::__construct();
		
    }
		
	
	/**
     * Used in delivery compilation: get the test included in an activity
	 * if found, it returns the delivery resource and null otherwise
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource activity
     * @return core_kernel_classes_Resource or null
     */	
	public function getTestByActivity(core_kernel_classes_Resource $activity){
		$returnValue = null;
		
		if(!empty($activity)){
			
			foreach ($activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->getIterator() as $iService){
				if($iService instanceof core_kernel_classes_Resource){
					
					$serviceDefinition = $iService->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
					
					//if service definition has the url of the service test process container
					$testContainerServiceDefinition = wfEngine_helpers_ProcessUtil::getServiceDefinition(TAO_TEST_CLASS);
					
					if(!is_null($testContainerServiceDefinition)){
						if($serviceDefinition->uriResource == $testContainerServiceDefinition->uriResource){
					
							foreach($iService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMIN))->getIterator() as $actualParam){
								
								$formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_FORMALPARAMETER));
								if($formalParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME)) == 'testUri'){
									$test = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_CONSTANTVALUE));
									if(!is_null($test)){
										$returnValue = $test;
										break(2);
									}
								}
							}
							
						}
					}
						
				}
				
			}
			
		}
		
		return $returnValue;
	}
	
	public function getTestProcessFromActivity(core_kernel_classes_Resource $activity){
		$returnValue = null;
		$test = $this->getTestByActivity($activity);
		if(!is_null($test)){
			$returnValue =  $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		}
		return $returnValue;
	}
	
	/**
     * Get the delivery associated to a process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource process
     * @return core_kernel_classes_Resource or null
     */	
	public function getDeliveryFromProcess(core_kernel_classes_Resource $process){
		
		$delivery = null;
		
		$deliveryCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_DELIVERYCONTENT,$process->uriResource);
		if(!$deliveryCollection->isEmpty()){
			$delivery = $deliveryCollection->get(0);
		}
		
		return $delivery;
	}
	
	

} /* end of class taoDelivery_models_classes_DeliveryAuthoringService */

?>