<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * returns the folder to store the compiled delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryService
    extends tao_models_classes_ClassService
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
     * @author Joel Bout, <joel@taotesting.com>
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
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneDelivery( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020AB begin
		//call the parent create instance to prevent useless process test to be created:
		$label = $instance->getLabel();
		$cloneLabel = "$label bis";
		if(preg_match("/bis/", $label)){
			$cloneNumber = (int)preg_replace("/^(.?)*bis/", "", $label);
			$cloneNumber++;
			$cloneLabel = preg_replace("/bis(.?)*$/", "", $label)."bis $cloneNumber" ;
		}
		$clone = parent::createInstance($clazz, $cloneLabel);

		if (!is_null($clone)) {
			$noCloningProperties = array(
				TAO_DELIVERY_DELIVERYCONTENT,
				TAO_DELIVERY_COMPILED_PROP,
				TAO_DELIVERY_PROCESS,
				RDF_TYPE
			);

			foreach($clazz->getProperties(true) as $property) {
				if(!in_array($property->getUri(), $noCloningProperties)) {
					//allow clone of every property value but the deliverycontent, which is a process:
					foreach($instance->getPropertyValues($property) as $propertyValue) {
						$clone->setPropertyValue($property, $propertyValue);
					}
				}
			}
			$clone->setLabel($cloneLabel);

			//clone the process:
			$propInstanceContent = new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT);
			try{
				$process = $instance->getUniquePropertyValue($propInstanceContent);
			}catch(Exception $e){}
			if(!is_null($process)){
				$processCloner = new wfAuthoring_models_classes_ProcessCloner();
				$processClone = $processCloner->cloneProcess($process);
				$clone->editPropertyValues($propInstanceContent, $processClone->getUri());
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
     * @author Joel Bout, <joel@taotesting.com>
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
     * @author Joel Bout, <joel@taotesting.com>
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
		$extManager = common_ext_ExtensionsManager::singleton();
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE), INSTANCE_ACL_ROLE);
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE), INSTANCE_ROLE_DELIVERY);

		$returnValue->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT), $processInstance->getUri());
		$this->updateProcessLabel($returnValue);

		//set the the default authoring mode to the 'simple mode':
		$returnValue->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP), TAO_DELIVERY_SIMPLEMODE);

		//set the default delivery server:
		$returnValue->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP), TAO_DELIVERY_DEFAULT_RESULT_SERVER);

		//set to active
		$returnValue->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_ACTIVE_PROP), GENERIS_TRUE);
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020AF end

        return $returnValue;
    }

    /**
     * Short description of method deleteDelivery
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
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
                $processAuthoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
                $processAuthoringService->deleteProcess($process);
                if(!is_null($actualProcess)) $processAuthoringService->deleteProcess($actualProcess);

                if($deleteHistory){
                        foreach ($this->getHistory($delivery) as $history){
                                $this->deleteHistory($history);
                        }
                }

                if($deleteCompiledFolder){
                	$folder = $this->getCompiledFolder($delivery);
                	if (file_exists($folder)) { 
						$returnValue = tao_helpers_File::remove($folder, true);
                	}
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
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B3 begin
		if(!is_null($clazz)){
			if($this->isDeliveryClass($clazz) && $clazz->getUri() != $this->deliveryClass->getUri()){
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
     * @author Joel Bout, <joel@taotesting.com>
     * @return array
     */
    public function getAllTests()
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B5 begin
		$testClazz = new core_kernel_classes_Class(TAO_TEST_CLASS);
		foreach($testClazz->getInstances(true) as $instance){
			$returnValue[$instance->getUri()] = $instance->getLabel();
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020B5 end

        return (array) $returnValue;
    }

    /**
     * returns the top delivery class
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Class
     */
    public function getRootClass()
    {
		return $this->deliveryClass;
    }

    /**
     * Returns tests of the delivery in order
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return array
     * @see getRelatedTests
     */
    public function getDeliveryTests( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BB begin
		$tests = array();
		$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();

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
				if($isIntial->getUri() == GENERIS_TRUE){
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
				$returnValue[$i] = $test;
			}

			//get its connector (check the type is "sequential) if ok, get the next activity
			$nextActivity = null;
			$connector = $currentActivity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
			if (!is_null($connector)) {
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				if($connectorType->getUri() == INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
					$nextActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
				} else {
					common_Logger::w('non sequential connector '.$connector->getUri().' in delivery');
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
		// section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BB end

        return (array) $returnValue;
    }

    /**
     * Short description of method getDeliveriesTests
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array
     */
    public function getDeliveriesTests()
    {
        $returnValue = array();

        // section 127-0-1-1-35b227b4:127a93c45f1:-8000:0000000000002346 begin
		foreach($this->deliveryClass->getInstances(true) as $delivery){
        	$returnValue[$delivery->getUri()] =  $this->getRelatedTests($delivery);
        }
        // section 127-0-1-1-35b227b4:127a93c45f1:-8000:0000000000002346 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getExcludedSubjects
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
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
					$subjectSubClasses[] = $subjectSubClass->getUri();
				}
				foreach($subjects as $subjectUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($subjectUri));
					if(!is_null($clazz)){
						if(in_array($clazz->getUri(), $subjectSubClasses)){
							$returnValue[] = $clazz->getUri();
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
     * @author Joel Bout, <joel@taotesting.com>
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
                $returnValue = $historyClass->searchInstances(array(TAO_DELIVERY_HISTORY_DELIVERY_PROP => $delivery->getUri()), array('like'=>false, 'recursive' => 0));

        }else{
                //select history by delivery and subject
                $returnValue = $historyClass->searchInstances(array(
                        TAO_DELIVERY_HISTORY_DELIVERY_PROP => $delivery->getUri(),
                        TAO_DELIVERY_HISTORY_SUBJECT_PROP => $subject->getUri()),
                array('like'=>false, 'recursive' => 0));
        }

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020BF end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProcessVariable
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string code
     * @return core_kernel_classes_Resource
     */
    public function getProcessVariable($code)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C1 begin
		$processVariableClass =  new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		$variables = $processVariableClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false, 'recursive' => 0));
		if(!empty($variables)){
			if($variables[0] instanceof core_kernel_classes_Resource){
				$returnValue = $variables[0];
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C1 end

        return $returnValue;
    }

    /**
     * Short description of method getResultServer
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
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
     * returns the tests of the delivery in no specific order
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return array
     * @see getDeliveryTests
     */
    public function getRelatedTests( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020C5 begin
		if(!is_null($delivery)){

			try{
			 	$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
			 	$process = $delivery->getUniquePropertyValue(
					new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT)
				);
				if(!is_null($process)){
					$activities = $authoringService->getActivitiesByProcess($process);

					foreach($activities as $activity){
						$test = $authoringService->getTestByActivity($activity);
						if(!is_null($test) && $test instanceof core_kernel_classes_Resource){
                                                        $test->getLabel();//make sure that the label is set
							$returnValue[$test->getUri()] = $test;
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
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return boolean
     */
    public function isCompiled( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CB begin

		$value = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP));
		if($value instanceof core_kernel_classes_Resource ){
			if ($value->getUri() == GENERIS_TRUE){
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
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Class clazz
     * @return boolean
     */
    public function isDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CD begin
		if($clazz->getUri() == $this->deliveryClass->getUri()){
			$returnValue = true;
		}
		else{
			foreach($this->deliveryClass->getSubClasses(true) as $subclass){
				if($clazz->getUri() == $subclass->getUri()){
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
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @param  array tests
     * @return boolean
     */
    public function setDeliveryTests( core_kernel_classes_Resource $delivery, $tests = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CF begin
		$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();

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
				throw new common_Exception("the interactive test of test {$test->getlabel()}({$test->getUri()})");
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
					$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT), $activity->getUri());
				}
				$previousConnector = $connector;//set the current connector as "the previous one" for the next loop
			}
			else{
				//if it is the last test of the array, no need to add a connector: just connect the previous connector to the last activity
				$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT), $activity->getUri());
				//every action is performed:
				$returnValue = true;
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:00000000000020CF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setExcludedSubjects
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
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
     * @author Joel Bout, <joel@taotesting.com>
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
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return boolean
     */
    public function linearizeDeliveryProcess( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--791be41d:12767a251df:-8000:00000000000021E5 begin
        $tests = array_values($this->getDeliveryTests($delivery));
        $returnValue = $this->setDeliveryTests($delivery,$tests);
        // section 10-13-1-39--791be41d:12767a251df:-8000:00000000000021E5 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteHistory
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
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
                                        $processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
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
     * @author Joel Bout, <joel@taotesting.com>
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
				$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
				$authoringService->deleteProcess($oldDeliveryProcess);
			}
			//then save it in TAO_DELIVERY_PROCESS prop:
			$delivery->editPropertyValues($propDeliveryProcess, $deliveryProcess->getUri());
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
     * @author Joel Bout, <joel@taotesting.com>
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
					if($aDelivery == $delivery->getUri()){
						$groups[] = $instance->getUri();
						break;
					}
				}
			}

			if(count($groups) > 0){
				$groupSubClasses = array();
				foreach($groupClass->getSubClasses(true) as $groupSubClass){
					$groupSubClasses[] = $groupSubClass->getUri();
				}
				foreach($groups as $groupUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($groupUri));
					if(!is_null($clazz)){
						if(in_array($clazz->getUri(), $groupSubClasses)){
							$returnValue[] = $clazz->getUri();
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
     * @author Joel Bout, <joel@taotesting.com>
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
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @param  array groups
     * @return boolean
     */
    public function setDeliveryGroups( core_kernel_classes_Resource $delivery, $groups)
    {
		$groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		$property = new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
		$currentGroups = $groupClass->searchInstances(array(
		    TAO_GROUP_DELIVERIES_PROP => $delivery
		), array('recursive' => true, 'like' => false));

		$toAdd = array();
		$toRemove = array();
		foreach ($currentGroups as $cGroup) {
		    $found = false;
		    foreach ($groups as $nGroup) {
		        if ($cGroup->equals($nGroup)) {
		            $found = true;
		            break;
		        }
		    }
		    if (!$found) {
		        $cGroup->removePropertyValue($property, $delivery);
		    }
		}
		foreach ($groups as $nGroup) {
		    $found = false;
		    foreach ($currentGroups as $cGroup) {
		        if ($cGroup->equals($nGroup)) {
		            $found = true;
		            break;
		        }
		    }
		    if (!$found) {
		        $nGroup->setPropertyValue($property, $delivery);
		    }
		}
        return (bool) true;
    }

    /**
     * Short description of method getCompiledDate
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
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

    /**
     * Short description of method containsHumanAssistedMeasurements
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return boolean
     */
    public function containsHumanAssistedMeasurements( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;

		foreach ($this->getDeliveryItems($delivery) as $item) {
			$measurements	= taoItems_models_classes_ItemsService::singleton()->getItemMeasurements($item);
	        foreach ($measurements as $measurement) {
	        	if ($measurement->isHumanAssisted()) {
	        		$returnValue = true;
	        		break(2);
	        	}
	        }
		}

        return (bool) $returnValue;
    }

    /**
     * Short description of method getDeliveryItems
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @param  boolean inOrder
     * @return array
     */
    public function getDeliveryItems( core_kernel_classes_Resource $delivery, $inOrder = false)
    {
        $returnValue = array();

        // section 127-0-1-1-74b053b:136828054b1:-8000:00000000000038E4 begin
        if ($inOrder) {
    		foreach ($this->getDeliveryTests($delivery) as $test) {
				foreach (taoTests_models_classes_TestsService::singleton()->getTestItems($test) as $item) {
					$returnValue[] = $item;
				}
			}
        } else {
		 	$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
		 	$process = $delivery->getUniquePropertyValue(
				new core_kernel_classes_Property(TAO_DELIVERY_PROCESS)
			);
			if(!is_null($process)){
				$activities = $authoringService->getActivitiesByProcess($process);

				foreach($activities as $activity){
					$item = $authoringService->getItemByActivity($activity);
					if (is_null($item)) {
					    throw new common_exception_Error("An item being referred to into this delivery does not exist anymore");
					}
					$returnValue[$item->getUri()] = $item;
				}
			}
        }
        // section 127-0-1-1-74b053b:136828054b1:-8000:00000000000038E4 end

        return (array) $returnValue;
    }

    /**
     * Short description of method isDeliveryOpen
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return boolean
     */
    public function isDeliveryOpen( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-60224649:1368791ec8c:-8000:00000000000038F0 begin
		$status = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_ACTIVE_PROP));
		if ($status->getUri() == GENERIS_TRUE) {
			$returnValue = true;
		}
        // section 127-0-1-1-60224649:1368791ec8c:-8000:00000000000038F0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method initDeliveryExecution
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource processDefinition
     * @param  Resource user
     * @return core_kernel_classes_Resource
     */
    public function initDeliveryExecution( core_kernel_classes_Resource $processDefinition,  core_kernel_classes_Resource $user)
    {
        $returnValue = null;

        // section 10-30-1--78-36889277:13cf288bd30:-8000:0000000000003C87 begin
        $deliveryServerService = taoDelivery_models_classes_DeliveryServerService::singleton();
		$deliveryAuthoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		
		$delivery = $deliveryAuthoringService->getDeliveryFromProcess($processDefinition);
		if(is_null($delivery)){
			throw new common_exception_Error("no delivery found for the selected process definition");
		}

		$wsdlContract = $deliveryServerService->getResultServer($delivery);
		if(empty($wsdlContract)){
			throw new common_exception_Error("no wsdl contract found for the current delivery");
		}

		// Initialize a process can be long depending on its complexity...
		set_time_limit(200);
		
		$processExecName = $delivery->getLabel();
		$processExecComment = 'Created in delivery server on ' . date(DATE_ISO8601);
		$processVariables = array();
		$var_delivery = new core_kernel_classes_Resource(INSTANCE_PROCESSVARIABLE_DELIVERY);
		if($var_delivery->hasType(new core_kernel_classes_Class(CLASS_PROCESSVARIABLES))){
			$processVariables[$var_delivery->getUri()] = $delivery->getUri();//no need to encode here, will be donce in Service::getUrlCall
		}else{
			throw new common_exception_Error('the required process variable "delivery" is missing in delivery server, tao install need to be fixed');
		}

		$returnValue = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $processVariables);
		
		//create nonce to initial activity executions:
		foreach($processExecutionService->getCurrentActivityExecutions($returnValue) as $initialActivityExecution){
			$activityExecutionService->createNonce($initialActivityExecution);
		}
		
		//add history of delivery execution in the delivery ontology
		$deliveryServerService->addHistory($delivery, $user, $returnValue);
        // section 10-30-1--78-36889277:13cf288bd30:-8000:0000000000003C87 end

        return $returnValue;
    }

    /**
     * returns the folder to store the compiled delivery
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return string
     */
    public function getCompiledFolder( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (string) '';

        // section 10-30-1--78--15e7ecbd:13cfbda82e1:-8000:0000000000003C8E begin
        $deliveryExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $deliveryFolderName = substr($delivery->getUri(), strpos($delivery->getUri(), '#') + 1);
		$returnValue = $deliveryExtension->getConstant('COMPILE_FOLDER').$deliveryFolderName.DIRECTORY_SEPARATOR;
        // section 10-30-1--78--15e7ecbd:13cfbda82e1:-8000:0000000000003C8E end

        return (string) $returnValue;
    }

    /**
     * Returns the folder in which the compiled test is stored
     * 
     * @param core_kernel_classes_Resource $delivery
     * @param core_kernel_classes_Resource $test
     * @return string
     */
    public function getCompiledTestFolder( core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $test)
    {
    	return $this->getCompiledFolder($delivery).substr($test->getUri(), strpos($test->getUri(), '#') + 1).DIRECTORY_SEPARATOR;
    }
    
    /**
     *
     * Returns the folder in which the compiled item is stored
     * returns the first language of the array provided that can be found
     * 
     * @param core_kernel_classes_Resource $delivery
     * @param core_kernel_classes_Resource $test
     * @param core_kernel_classes_Resource $item
     * @param core_kernel_classes_Resource $languages
     * @return string
     */
    public function getCompiledItemFolder( core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $test, core_kernel_classes_Resource $item, $languages)
    {
    	$base = $this->getCompiledTestFolder($delivery, $test)
    		.substr($item->getUri(), strpos($item->getUri(), '#') + 1)
    		.DIRECTORY_SEPARATOR;
    	foreach ($languages as $lang) {
    		if (is_dir($base.$lang)) {
    			return $base.$lang.DIRECTORY_SEPARATOR;
    		}
    	}
    	// language not found:
    	throw new common_Exception('no matching language itemfolder found at '.$base);
	}
    
} /* end of class taoDelivery_models_classes_DeliveryService */

?>