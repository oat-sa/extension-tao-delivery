<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery/models/classes/class.DeliveryAuthoringService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 26.10.2012, 14:40:17 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfAuthoring_models_classes_ProcessService
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('wfAuthoring/models/classes/class.ProcessService.php');

/* user defined includes */
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201C-includes begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201C-includes end

/* user defined constants */
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201C-constants begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201C-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryAuthoringService
    extends wfAuthoring_models_classes_ProcessService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function __construct()
    {
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000205F begin
		parent::__construct();
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000205F end
    }

    /**
     * Get the delivery associated to a process
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return core_kernel_classes_Resource
     */
    public function getDeliveryFromProcess( core_kernel_classes_Resource $process)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002061 begin
		
		$deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		$deliveries = $deliveryClass->searchInstances(array(TAO_DELIVERY_PROCESS => $process->uriResource), array('like'=>false, 'recursive' => 1000));
		
		if(!empty($deliveries))
		{
			$returnValue = array_pop($deliveries);
		}
		
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002061 end

        return $returnValue;
    }

    /**
     * Used in delivery compilation: get the test included in an activity
     * If found, it returns the delivery resource and null otherwise
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function getTestByActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002063 begin
		
		if(!empty($activity)){
			
			foreach ($activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->getIterator() as $iService){
				if($iService instanceof core_kernel_classes_Resource){
					
					$serviceDefinition = $iService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
					if(!is_null($serviceDefinition)){
					
						if($serviceDefinition->uriResource == INSTANCE_SERVICEDEFINITION_TESTCONTAINER){
				
							foreach($iService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN))->getIterator() as $actualParam){
								
								$formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER));
								if($formalParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME)) == 'testUri'){
									$test = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE));
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
		
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002063 end

        return $returnValue;
    }

    /**
     * Short description of method getItemByActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function getItemByActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 127-0-1-1-74b053b:136828054b1:-8000:00000000000038F1 begin
    	$service = $activity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
		foreach ($service->getPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN)) as $parameter) {
			$parameterRessource = new core_kernel_classes_Resource($parameter);
			$paramvals = $parameterRessource->getPropertiesValues(array(
				new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER),
				new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE)
			));
        		
			$formal = array_pop($paramvals[PROPERTY_ACTUALPARAMETER_FORMALPARAMETER]);
			if ($formal->getUri() == INSTANCE_FORMALPARAM_ITEMURI && isset($paramvals[PROPERTY_ACTUALPARAMETER_CONSTANTVALUE])) {
				$returnValue = array_pop($paramvals[PROPERTY_ACTUALPARAMETER_CONSTANTVALUE]);
				break;
			}
		}
        // section 127-0-1-1-74b053b:136828054b1:-8000:00000000000038F1 end

        return $returnValue;
    }

    /**
     * Short description of method getTestProcessFromActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function getTestProcessFromActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E28 begin
		
		$test = $this->getTestByActivity($activity);
		if(!is_null($test)){
			$returnValue =  $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		}
		
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E28 end

        return $returnValue;
    }

    /**
     * Short description of method setTestByActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @param  Resource test
     * @return core_kernel_classes_Resource
     */
    public function setTestByActivity( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $test)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E2B begin
		
		if(!is_null($activity) && !is_null($test)){
		
			
			
			//set property value visible to true
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), GENERIS_FALSE);
			
			//set ACL mode to role user restricted with role=subject
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE), INSTANCE_ACL_ROLE);//should be eventually INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE), CLASS_ROLE_SUBJECT);
			
			//get the item runner service definition: must exists!
			$testContainerServiceDefinition = new core_kernel_classes_Resource(INSTANCE_SERVICEDEFINITION_TESTCONTAINER);
			if(!$testContainerServiceDefinition->hasType(new core_kernel_classes_Class(CLASS_SUPPORTSERVICES))){
				throw new Exception('the required service definition test container does not exists, reinstall tao is required');
			}
			
			//create a call of service and associate the service definition to it:
			$interactiveService = $this->createInteractiveService($activity);
			$interactiveService->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $testContainerServiceDefinition->uriResource);
			
			//get formal param associated to the test definition (defined in the delivery model so undeletable)
			$testUriParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_TESTURI);
			
			//create an actual parameter to the service:
			$this->setActualParameter($interactiveService, $testUriParam, $test->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_CONSTANTVALUE);
			
			$returnValue = $interactiveService;
		}
		
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E2B end

        return $returnValue;
    }

    /**
     * Short description of method getDeliveryContentFromProcess
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return core_kernel_classes_Resource
     */
    public function getDeliveryContentFromProcess( core_kernel_classes_Resource $process)
    {
        $returnValue = null;

        // section 127-0-1-1--4d76012c:1374bad4278:-8000:0000000000003A72 begin
        
        $deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
        $deliveries = $deliveryClass->searchInstances(array(TAO_DELIVERY_DELIVERYCONTENT => $process->uriResource), array('like'=>false, 'recursive' => 1000));
        
        if(!empty($deliveries))
        {
        	$returnValue = array_pop($deliveries);
        }
        
        // section 127-0-1-1--4d76012c:1374bad4278:-8000:0000000000003A72 end

        return $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryAuthoringService */

?>