<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery/models/classes/class.DeliveryService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.06.2011, 13:21:07 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002020-includes begin
require_once(dirname(__FILE__).'/class.DeliveryProcessGenerator.php');
// section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002020-includes end

/* user defined constants */
// section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002020-constants begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002020-constants end

/**
 * Short description of class taoDelivery_models_classes_DeliveryService
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute deliveryClass
     *
     * @access protected
     * @var Class
     */
    protected $deliveryClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020A9 begin
		parent::__construct();
		$this->deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020A9 end
    }

    /**
     * Short description of method cloneDelivery
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneDelivery( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020AB begin
		//call the parent create instance to prevent useless process test to be created:
		$clone = parent::createInstance($clazz, $instance->getLabel()." bis");
		
		if(!is_null($clone)){
			$noCloningProperties = array(
				TAO_DELIVERY_DELIVERYCONTENT,
				TAO_DELIVERY_COMPILED_PROP,
				TAO_DELIVERY_PROCESS,
				RDF_TYPE
			);
		
			foreach($clazz->getProperties(true) as $property){
			
				if(!in_array($property->uriResource, $noCloningProperties)){
					//allow clone of every property value but the deliverycontent, which is a process:
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$clone->setPropertyValue($property, $propertyValue);
					}
				}
				
			}
			
			//clone the process:
			$propInstanceContent = new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT);
			try{
				$process = $instance->getUniquePropertyValue($propInstanceContent);
			}catch(Exception $e){}
			if(!is_null($process)){
				$processCloner = new wfEngine_models_classes_ProcessCloner();
				$processClone = $processCloner->cloneProcess($process);
				$clone->editPropertyValues($propInstanceContent, $processClone->uriResource);
			}else{
				throw new Exception("the delivery process cannot be found");
			}
			
			$this->updateProcessLabel($clone);
			$returnValue = $clone;
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020AB end

        return $returnValue;
    }

    /**
     * Short description of method createDeliveryClass
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createDeliveryClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020AD begin

		if(is_null($clazz)){
			$clazz = $this->deliveryClass;
		}
		
		if($this->isDeliveryClass($clazz)){
		
			$deliveryClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $deliveryClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' delivery property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
			}
			$returnValue = $deliveryClass;
		}

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020AD end

        return $returnValue;
    }

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Class $clazz, $label = '')
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020AF begin
		$returnValue = parent::createInstance($clazz, $label);
		
		//create a process instance at the same time:
		$processInstance = parent::createInstance(new core_kernel_classes_Class(CLASS_PROCESS),'process generated with deliveryService');
		
		//set ACL right to delivery process initialization:
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE), INSTANCE_ACL_ROLE);
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE), CLASS_ROLE_SUBJECT);
			
		$returnValue->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT), $processInstance->uriResource);
		$this->updateProcessLabel($returnValue);
		
		//set the the default authoring mode to the 'simple mode':
		$returnValue->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP), TAO_DELIVERY_SIMPLEMODE);
		
		//set the default delivery server:
		$returnValue->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP), TAO_DELIVERY_DEFAULT_RESULT_SERVER);
		
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020AF end

        return $returnValue;
    }

    /**
     * Short description of method deleteDelivery
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @param  boolean deleteHistory
     * @param  boolean deleteCompiledFolder
     * @return boolean
     */
    public function deleteDelivery( core_kernel_classes_Resource $delivery, $deleteHistory = true, $deleteCompiledFolder = true)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B1 begin
        
        if(!is_null($delivery)){
                //delete the process associated to the delivery:
                $process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
                $actualProcess = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_PROCESS));
                $processAuthoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
                $processAuthoringService->deleteProcess($process);
                if(!is_null($actualProcess)) $processAuthoringService->deleteProcess($actualProcess);

                if($deleteHistory){
                        foreach ($this->getHistory($delivery) as $history){
                                $this->deleteHistory($history);
                        }
                }
                
                if($deleteCompiledFolder){
                        $deliveryFolderName = substr($delivery->uriResource, strpos($delivery->uriResource, '#') + 1);
                        $path = BASE_PATH."/compiled/$deliveryFolderName";
                        $returnValue = tao_helpers_File::remove($path, true);
                }

                $returnValue = $delivery->delete();
        }
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteDeliveryClass
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B3 begin
		if(!is_null($clazz)){
			if($this->isDeliveryClass($clazz) && $clazz->uriResource != $this->deliveryClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllTests
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getAllTests()
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B5 begin
		$testClazz = new core_kernel_classes_Class(TAO_TEST_CLASS);
		foreach($testClazz->getInstances(true) as $instance){
			$returnValue[$instance->uriResource] = $instance->getLabel();
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getDelivery
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getDelivery($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B7 begin
		if(is_null($clazz)){
			$clazz = $this->deliveryClass;
		}
		if($this->isDeliveryClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B7 end

        return $returnValue;
    }

    /**
     * Short description of method getDeliveryClass
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getDeliveryClass($uri = '')
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B9 begin
		if(empty($uri) && !is_null($this->deliveryClass)){
			$returnValue = $this->deliveryClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isDeliveryClass($clazz)){
				$returnValue = $clazz;
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B9 end

        return $returnValue;
    }

    /**
     * Short description of method getDeliveryTests
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return array
     */
    public function getDeliveryTests( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BB begin
		$tests = array();
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		//get the associated process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		$totalNumber = count($activities);
		
		//find the first one: property isinitial == true (must be only one, if not error) and set as the currentActivity:
		$currentActivity = null;
		foreach($activities as $activity){
			
			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->uriResource == GENERIS_TRUE){
					$currentActivity = $activity;
					break;
				}
			}
		}
		
		if(is_null($currentActivity)){
			return $tests;
		}
		
		//start the loop:
		for($i=0;$i<$totalNumber;$i++){
			$test = $authoringService->getTestByActivity($currentActivity);
			if(!is_null($test)){
				$tests[$i] = $test;
			}
			
			//get its connector (check the type is "sequential) if ok, get the next activity
			$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
			$connectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES =>$currentActivity->uriResource), array('like'=>false));
			$nextActivity = null;
			foreach($connectors as $connector){
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
					$nextActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
					break;
				}
			}
			if(!is_null($nextActivity)){
				$currentActivity = $nextActivity;
			}else{
				if($i == $totalNumber-1){
					//it is normal, since it is the last activity and test
				}else{
					throw new Exception('the next activity of the connector is not found');
				}	
			}
		}
		
		if(count($tests) > 0){
			
			ksort($tests);
			
			$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
			$testSubClasses = array();
			foreach($testClass->getSubClasses(true) as $testSubClass){
				$testSubClasses[] = $testSubClass->uriResource;
			}
			
			foreach($tests as $test){
				$clazz = $this->getClass($test);
				if(in_array($clazz->uriResource, $testSubClasses)){
					$returnValue[] = $clazz;
				}
				$returnValue[] = $test;
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BB end

        return (array) $returnValue;
    }

    /**
     * Short description of method getDeliveriesTests
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getDeliveriesTests()
    {
        $returnValue = array();

        // section 127-0-1-1-35b227b4:127a93c45f1:-8000:0000000000002346 begin
	foreach($this->deliveryClass->getInstances(true) as $delivery){
        	$returnValue[$delivery->uriResource] =  $this->getRelatedTests($delivery);
        }
        // section 127-0-1-1-35b227b4:127a93c45f1:-8000:0000000000002346 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getExcludedSubjects
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return array
     */
    public function getExcludedSubjects( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BD begin
		if(!is_null($delivery)){
			$subjects = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP));
		
			if(count($subjects) > 0){
				$subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
				$subjectSubClasses = array();
				foreach($subjectClass->getSubClasses(true) as $subjectSubClass){
					$subjectSubClasses[] = $subjectSubClass->uriResource;
				}
				foreach($subjects as $subjectUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($subjectUri));
					if(!is_null($clazz)){
						if(in_array($clazz->uriResource, $subjectSubClasses)){
							$returnValue[] = $clazz->uriResource;
						}
					}
					$returnValue[] = $subjectUri;
				}
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BD end

        return (array) $returnValue;
    }

    /**
     * Short description of method getHistory
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @param  Resource subject
     * @return array
     */
    public function getHistory( core_kernel_classes_Resource $delivery,  core_kernel_classes_Resource $subject = null)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BF begin
        $historyClass = new core_kernel_classes_Class(TAO_DELIVERY_HISTORY_CLASS);
        if(empty($subject)){
                //select History by delivery only (subject independent listing, i.e. select for all subjects)
                $returnValue = $historyClass->searchInstances(array(TAO_DELIVERY_HISTORY_DELIVERY_PROP => $delivery->uriResource), array('like'=>false, 'recursive' => true));

        }else{
                //select history by delivery and subject
                $returnValue = $historyClass->searchInstances(array(
                        TAO_DELIVERY_HISTORY_DELIVERY_PROP => $delivery->uriResource, 
                        TAO_DELIVERY_HISTORY_SUBJECT_PROP => $subject->uriResource), 
                array('like'=>false, 'recursive' => true));
        }
				
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BF end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string code
     * @return core_kernel_classes_Resource
     */
    public function getProcessVariable($code)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C1 begin
		$processVariableClass =  new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		$variables = $processVariableClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false, 'recursive' => false));
		if(!empty($variables)){
			if($variables[0] instanceof core_kernel_classes_Resource){
				$returnValue = $variables[0];
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C1 end

        return $returnValue;
    }

    /**
     * Short description of method getRelatedCampaigns
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return array
     */
    public function getRelatedCampaigns( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C3 begin
		if(!is_null($delivery)){
			$campaigns = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP));
		
			if(count($campaigns)>0){
				$campaignClass =  new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
				$campaignSubClasses = array();
				foreach($campaignClass->getSubClasses(true) as $campaignSubClass){
					$campaignSubClasses[] = $campaignSubClass->uriResource;
				}
				foreach($campaigns as $campaignUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($campaignUri));
					if(!is_null($clazz)){
						if(in_array($clazz->uriResource, $campaignSubClasses)){
							$returnValue[] = $clazz->uriResource;
						}
					}
					$returnValue[] = $campaignUri;
				}
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C3 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getResultServer
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return core_kernel_classes_Resource
     */
    public function getResultServer( core_kernel_classes_Resource $delivery)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C7 begin
		if(!is_null($delivery)){
			$returnValue = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C7 end

        return $returnValue;
    }

    /**
     * Short description of method getRelatedTests
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return array
     */
    public function getRelatedTests( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C5 begin
		if(!is_null($delivery)){
		
			try{
			 	$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
			 	$process = $delivery->getUniquePropertyValue(
					new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT)
				);
				if(!is_null($process)){
					$activities = $authoringService->getActivitiesByProcess($process);
				
					foreach($activities as $activity){
						$test = $authoringService->getTestByActivity($activity);
						if(!is_null($test) && $test instanceof core_kernel_classes_Resource){
                                                        $test->getLabel();//make sure that the label is set
							$returnValue[$test->uriResource] = $test;
						}
					}
				}
			}
			catch(Exception $e){}
		
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method isCompiled
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return boolean
     */
    public function isCompiled( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CB begin
		
		$value = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP));
		if($value instanceof core_kernel_classes_Resource ){
			if ($value->uriResource == GENERIS_TRUE){
				$returnValue = true;
			}
		}
		
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isDeliveryClass
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CD begin
		if($clazz->uriResource == $this->deliveryClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->deliveryClass->getSubClasses(true) as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CD end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setDeliveryTests
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @param  array tests
     * @return boolean
     */
    public function setDeliveryTests( core_kernel_classes_Resource $delivery, $tests = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CF begin
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		// get the current process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
					
		//delete all related activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		foreach($activities as $activity){
			if(!$authoringService->deleteActivity($activity)){
				return $returnValue;
			}
		}
		
		
		//create the list of activities and interactive services and tests plus their appropriate property values:
		$totalNumber = count($tests);//0...n
		$previousConnector = null; 
		for($i=0;$i<$totalNumber;$i++){
			$test = $tests[$i];
			if(!($test instanceof core_kernel_classes_Resource)){
				throw new Exception("the array element n$i is not a Resource");
			}
			
			//create an activity
			$activity = null;
			$activity = $authoringService->createActivity($process, "test: {$test->getLabel()}");
			if($i==0){
				//set the property value as initial
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
			}
			
			$interactiveService = $authoringService->setTestByActivity($activity, $test);
			if(is_null($interactiveService)){
				throw new core_kernel_classes_Resource("the interactive test of test {$test->getlabel()}({$test->uriResource})");
			}
			
			if($totalNumber == 1){
				if(!is_null($interactiveService) && $interactiveService instanceof core_kernel_classes_Resource){
					return true;
				}
			}
			if($i<$totalNumber-1){
				//get the connector created as the same time as the activity and set the type to "sequential" and the next activity as the selected service definition:
				$connector = $authoringService->createConnector($activity);
				if(!($connector instanceof core_kernel_classes_Resource) || is_null($connector)){
					throw new Exception("the created connector is not a resource");
					return $returnValue;
				}
			
				$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
				
				if(!is_null($previousConnector)){
					$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $activity->uriResource);
				}
				$previousConnector = $connector;//set the current connector as "the previous one" for the next loop	
			}
			else{
				//if it is the last test of the array, no need to add a connector: just connect the previous connector to the last activity
				$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $activity->uriResource);
				//every action is performed:
				$returnValue = true;
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setRelatedCampaigns
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @param  array campaigns
     * @return boolean
     */
    public function setRelatedCampaigns( core_kernel_classes_Resource $delivery, $campaigns = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020D1 begin
		if(!is_null($delivery)){
			
			$campaignProp = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);
			
			$delivery->removePropertyValues($campaignProp);
			$done = 0;
			foreach($campaigns as $campaign){
				if($delivery->setPropertyValue($campaignProp, $campaign)){
					$done++;
				}
			}
			if($done == count($campaigns)){
				$returnValue = true;
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020D1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setExcludedSubjects
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @param  array subjects
     * @return boolean
     */
    public function setExcludedSubjects( core_kernel_classes_Resource $delivery, $subjects = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020D3 begin
		if(!is_null($delivery)){
			
			$memberProp = new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP);
			
			$delivery->removePropertyValues($memberProp);
			$done = 0;
			foreach($subjects as $subject){
				if($delivery->setPropertyValue($memberProp, $subject)){
					$done++;
				}
			}
			if($done == count($subjects)){
				$returnValue = true;
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020D3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method updateProcessLabel
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return mixed
     */
    public function updateProcessLabel( core_kernel_classes_Resource $delivery)
    {
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020D5 begin
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		$process->setLabel("Process ".$delivery->getLabel());
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020D5 end
    }

    /**
     * Short description of method linearizeDeliveryProcess
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return boolean
     */
    public function linearizeDeliveryProcess( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--791be41d:12767a251df:-8000:00000000000021E5 begin
		//get list of all tests in the delivery, without order:
		$tests = array();
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		//get the associated process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		
		foreach($activities as $activity){
			
			//get the FIRST interactive service
			$iService = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			if(!is_null($iService)){
				
				//get the service definition
				$serviceDefinition = $iService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
				if(!is_null($serviceDefinition)){
					
					//get the url
					$serviceUrl = $serviceDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL));
					if(!is_null($serviceUrl)){
					
						//regenerate the test uri
						$testUri = taoDelivery_helpers_Compilator::getTestUri($serviceUrl);
						if(!empty($testUri)){
							//set the test in the table:
							$tests[$testUri] = new core_kernel_classes_Resource($testUri);
						}
						
					}
					
				}
				
			}
			
		}
		//the functuon setDeliveryTests require an array with numerical key 
		$numericalKeyTestArray = array();
		foreach($tests as $test){
			$numericalKeyTestArray[] = $test;
		}
		
		$returnValue = $this->setDeliveryTests($delivery, $numericalKeyTestArray);
        // section 10-13-1-39--791be41d:12767a251df:-8000:00000000000021E5 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method compileTest
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @param  Resource test
     * @return array
     */
    public function compileTest( core_kernel_classes_Resource $delivery,  core_kernel_classes_Resource $test)
    {
        $returnValue = array();

        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CE6 begin
		$resultArray = array(
			'success' => 0,
			'failed' => array()
		);
				
		//preliminary check
		if(is_null($test)){
			throw new Exception('no empty test is allowed in compilation');
		}
		
		$testService = tao_models_classes_ServiceFactory::get('Tests');
		$itemService = tao_models_classes_ServiceFactory::get('Items');
		$items = $testService->getRelatedItems($test);
		
		$compilationResult = array();
		foreach($items as $item){
			//check if the item exists: if not, append to the test failure message
			$itemClasses = $item->getType();
			foreach($itemClasses as $itemClass){
				if(!is_null($itemClass) && !is_null($itemService->isItemModelDefined($item))){
					
					try{
						
						$itemFolderName = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
						$deliveryFolderName = substr($delivery->uriResource, strpos($delivery->uriResource, '#') + 1);
						$testFolderName = substr($test->uriResource, strpos($test->uriResource, '#') + 1);
						
						//create the compilation folder for the delivery-test-item:
						$compiledFolder = BASE_PATH."/compiled/$deliveryFolderName";
						if(!is_dir($compiledFolder)){
							mkdir($compiledFolder);
						}
						$compiledFolder .= "/$testFolderName";
						if(!is_dir($compiledFolder)){
							mkdir($compiledFolder);
						}
						$compiledFolder .= "/$itemFolderName";
						if(!is_dir($compiledFolder)){
							mkdir($compiledFolder);
						}
						$itemPath = "{$compiledFolder}/index.html";
						$itemUrl = str_replace(ROOT_PATH , ROOT_URL, $itemPath);
						
						$compilator = new taoDelivery_helpers_Compilator($delivery->uriResource, $test->uriResource, $item->uriResource, $compiledFolder);
						$compilator->clearCompiledFolder();
						
						$www = dirname($itemUrl);
					
						$deployParams = array(
							'delivery_server_mode'	=> true,
							'preview_mode'		=> false,
							'tao_base_www'		=> $www,
							'qti_base_www'		=> $www,
							'base_www' 		=> $www,
							'root_url'		=> ROOT_URL
						);
					
						//deploy the item
						$itemService->deployItem($item, $itemPath, $itemUrl,  $deployParams);
						
						if($itemService->hasItemModel($item, array(TAO_ITEM_MODEL_QTI))){
							$compilator->copyPlugins(array('js', 'css', 'img'));
						}
						else if($itemService->hasItemModel($item, array( TAO_ITEM_MODEL_HAWAI, TAO_ITEM_MODEL_XHTML))){
							$compilator->copyPlugins(array('js'));
						}
						else if($itemService->hasItemModel($item, array(TAO_ITEM_MODEL_KOHS, TAO_ITEM_MODEL_CTEST))){
							$compilator->copyPlugins(array('swf', 'js'));
						}
						else{
							$compilator->copyPlugins(array('js'));
						}
						
						//directory where all files required to launch the test will be collected
						$directory = $compilator->getCompiledPath();
						
						//parse the XML file with the helper compilator: media files are downloaded and a new xml file is generated, by replacing the new path for these media with the old ones
						$itemContent = $compilator->itemParser(file_get_contents($itemPath), $directory, "index.html");
								
						//create and write the new xml file in the folder of the test of the delivery being compiled (need for this so to enable LOCAL COMPILED access to the media)
						$compilator->stringToFile($itemContent, $directory, "index.html");
						
						$compilationResult[] = $compilator->result();
						
						
					}
					catch(Exception $e){
						$resultArray["failed"]["errorMsg"][] = $e->getMessage();
					}
				}else{
					//the item no longer exists, set error message and break the loop and thus the compilation:
					if(!isset($resultArray["failed"]['unexistingItems'])){
						$resultArray["failed"]['unexistingItems'] = array();
					}
					$resultArray["failed"]['unexistingItems'][$item->uriResource] = $item;
					continue;
				}
				break;
			}
		}		
		
		if(empty($resultArray["failed"])){
			$resultArray["success"] = 1;
		}
		$returnValue = $resultArray;
		
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CE6 end

        return (array) $returnValue;
    }

    /**
     * Short description of method deleteHistory
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource history
     * @param  boolean deleteProcessInstance
     * @return boolean
     */
    public function deleteHistory( core_kernel_classes_Resource $history, $deleteProcessInstance = true)
    {
        $returnValue = (bool) false;

        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CEA begin
		if(!is_null($history)){
                        if($deleteProcessInstance){
                                $relatedProcessExecution = $history->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_PROCESS_INSTANCE));
                                if(!is_null($relatedProcessExecution)){
                                        $processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
                                        $processExecutionService->deleteProcessExecution($relatedProcessExecution);
                                }
                        }
                        
			$returnValue = $history->delete();
		}
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CEA end

        return (bool) $returnValue;
    }

    /**
     * Short description of method generateProcess
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return array
     */
    public function generateProcess( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CEE begin
		$returnValue = array(
			'success' => false
		);
		
		$deliveryProcessGenerator = new taoDelivery_models_classes_DeliveryProcessGenerator();
		$deliveryProcess = $deliveryProcessGenerator->generateDeliveryProcess($delivery);
		if(!is_null($deliveryProcess)){
			//delete the old delivery process if exists:
			$propDeliveryProcess = new core_kernel_classes_Property(TAO_DELIVERY_PROCESS);
			$oldDeliveryProcess = $delivery->getOnePropertyValue($propDeliveryProcess);
			
			// print_r($oldDeliveryProcess);
			if($oldDeliveryProcess instanceof core_kernel_classes_Resource){
				$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
				$authoringService->deleteProcess($oldDeliveryProcess);
			}
			//then save it in TAO_DELIVERY_PROCESS prop:
			$delivery->editPropertyValues($propDeliveryProcess, $deliveryProcess->uriResource);
			$returnValue['success'] = true; 
		}else{
			$returnValue['errors'] = $deliveryProcessGenerator->getErrors();
		}
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CEE end

        return (array) $returnValue;
    }

    /**
     * Short description of method getDeliveryGroups
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return array
     */
    public function getDeliveryGroups( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CF6 begin
		if(!is_null($delivery)){
			$groupClass 		= new core_kernel_classes_Class(TAO_GROUP_CLASS);
			$deliveriesProperty	= new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
			
			$groups = array();
			
			foreach($groupClass->getInstances(true) as $instance){
				foreach($instance->getPropertyValues($deliveriesProperty) as $aDelivery){
					if($aDelivery == $delivery->uriResource){
						$groups[] = $instance->uriResource;
						break;
					}
				}
			}
			
			if(count($groups) > 0){
				$groupSubClasses = array();
				foreach($groupClass->getSubClasses(true) as $groupSubClass){
					$groupSubClasses[] = $groupSubClass->uriResource;
				}
				foreach($groups as $groupUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($groupUri));
					if(!is_null($clazz)){
						if(in_array($clazz->uriResource, $groupSubClasses)){
							$returnValue[] = $clazz->uriResource;
						}
					}
					$returnValue[] = $groupUri;
				}
			}
			
		}
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CF6 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setAuthoringMode
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @param  string mode
     * @return boolean
     */
    public function setAuthoringMode( core_kernel_classes_Resource $delivery, $mode)
    {
        $returnValue = (bool) false;

        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CFF begin
		$property = new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP);
		switch(strtolower($mode)){
			case 'simple':{
				$delivery->editPropertyValues($property, TAO_DELIVERY_SIMPLEMODE);
				//linearization required:
				$returnValue = $this->linearizeDeliveryProcess($delivery);
				break;
			}
			case 'advanced':{
				$returnValue = $delivery->editPropertyValues($property, TAO_DELIVERY_ADVANCEDMODE);
				break;
			}
			default:{
				$returnValue = false;
			}
		}
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002CFF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setDeliveryGroups
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @param  array groups
     * @return boolean
     */
    public function setDeliveryGroups( core_kernel_classes_Resource $delivery, $groups)
    {
        $returnValue = (bool) false;

        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002D03 begin
		if(!is_null($delivery)){
			$groupClass 		= new core_kernel_classes_Class(TAO_GROUP_CLASS);
			$deliveriesProperty	= new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
			
			$done = 0;
			foreach($groupClass->getInstances(true) as $instance){
				$newDeliveries = array();
				$updateIt = false;
				foreach($instance->getPropertyValues($deliveriesProperty) as $aDelivery){
					if($aDelivery == $delivery->uriResource){
						$updateIt = true;
					}
					else{
						$newDeliveries[] = $aDelivery;
					}
				}
				if($updateIt){
					$instance->removePropertyValues($deliveriesProperty);
					foreach($newDeliveries as $newDelivery){
						$instance->setPropertyValue($deliveriesProperty, $newDelivery);
					}
				}
				if(in_array($instance->uriResource, $groups)){
					if($instance->setPropertyValue($deliveriesProperty, $delivery->uriResource)){
						$done++;
					}
				}
			}
			if($done == count($groups)){
				$returnValue = true;
			}
		}
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000002D03 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCompiledDate
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource delivery
     * @return string
     */
    public function getCompiledDate( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (string) '';

        // section 10-13-1--128-4f552b1e:13008b73005:-8000:0000000000002E4A begin
		try{
			$modificationDate = $delivery->getLastModificationDate(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP));
			if(!is_null($modificationDate)){
				$returnValue = $modificationDate->format('d/m/Y H:i:s');
			}
		}catch(core_kernel_persistence_ProhibitedFunctionException $e){
			$returnValue = __('(date not available for hardified resource)');
		}
        // section 10-13-1--128-4f552b1e:13008b73005:-8000:0000000000002E4A end

        return (string) $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryService */

?>