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
	public function setTestByActivity(core_kernel_classes_Resource $activity, core_kernel_classes_Resource $test){
		
		$returnValue = null;
		
		if(!is_null($activity) && !is_null($test)){
		
			//create formal param associated to the test definition
			$testUriParam = $this->getFormalParameter('testUri');//it is alright if the default value (i.e. proc var has been changed)
			if(is_null($testUriParam)){
				$testUriParam = $this->createFormalParameter('testUri', 'constant', '', 'test uri (authoring)');
			}
			
			//set property value visible to true
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), GENERIS_FALSE);
			
			//set ACL mode to role user restricted with role=subject
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE), INSTANCE_ACL_ROLE);//should be eventually INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE), CLASS_ROLE_SUBJECT);
			
			$serviceDefinition = wfEngine_helpers_ProcessUtil::getServiceDefinition(TAO_TEST_CLASS);//use the TAO_TEST_CLASS as the key to identify test services
			if(is_null($serviceDefinition)){
				//if no corresponding service def found, create a service definition:
				$serviceDefinitionClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
				$serviceDefinition = $serviceDefinitionClass->createInstance('test process container', 'created by delivery service');
				
				//set service definition (the test) and parameters:
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL), TAO_TEST_CLASS);
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $testUriParam->uriResource);
			}
			
			//create a call of service and associate the service definition to it:
			$interactiveService = $this->createInteractiveService($activity);
			$interactiveService->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
			$this->setActualParameter($interactiveService, $testUriParam, $test->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMIN, PROPERTY_ACTUALPARAM_CONSTANTVALUE);
			
			$returnValue = $interactiveService;
		}
		
		return $returnValue;
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
	public function getDeliveryFromProcess(core_kernel_classes_Resource $process, $executionProcess = false){
		
		$delivery = null;
		
		$propDeliveryProcess = TAO_DELIVERY_DELIVERYCONTENT;
		if($executionProcess){
			$propDeliveryProcess = TAO_DELIVERY_PROCESS;
		}
		
		$deliveryCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject($propDeliveryProcess, $process->uriResource);
		if(!$deliveryCollection->isEmpty()){
			$delivery = $deliveryCollection->get(0);
		}
		
		return $delivery;
	}
	
	

} /* end of class taoDelivery_models_classes_DeliveryAuthoringService */

?>