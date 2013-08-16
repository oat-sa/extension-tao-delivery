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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * The default implementation of the result server,
 * to be used when no explicit external result server
 * is defined.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoDelivery_actions_ResultDelivery
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */

//require_once('taoDelivery/actions/class.ResultDelivery.php');

/**
 * include taoDelivery_models_classes_ResultServerInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoDelivery/models/classes/interface.ResultServerInterface.php');

/* user defined includes */
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003837-includes begin
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003837-includes end

/* user defined constants */
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003837-constants begin
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003837-constants end

/**
 * The default implementation of the result server,
 * to be used when no explicit external result server
 * is defined.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 */
class taoDelivery_actions_InternalResultServer
    //disabling the call to the legacy ResultDelivery Service, TODO PPL update the configured result servers
    //extends taoDelivery_actions_ResultDelivery
    extends taoDelivery_actions_DeliveryApi
        implements taoDelivery_models_classes_ResultServerInterface
{

    /**
     * Short description of attribute DELIVERYRESULT_SESSION_SERIAL
     *
     * @access private
     * @var string
     */
    const DELIVERYRESULT_SESSION_SERIAL = 'resultserver_dr';

    /**
     * The Result Service associated to this module.
     * @var taoResults_models_classes_ResultsService
     */
    private $resultService = null;

    /**
     * Save the data that is pushed to the server
     * this can be either answers, scores or variables
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function save()
    {
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383B begin
        $saved = false;
        // save Answers
    	if($this->hasRequestParameter('taoVars') && is_array($this->getRequestParameter('taoVars'))){
    		$executionEnvironment = $this->getExecutionEnvironment();
			$resultNS = $executionEnvironment['localNamespace'];
		
			//here we save the TAO variables
			$taoVars = array();
			$variableService = wfEngine_models_classes_VariableService::singleton();
			
			foreach($this->getRequestParameter('taoVars') as $key => $encoded){
				
				list ($namespace, $suffix) = explode('#', $key, 2);
				switch ($suffix) {
					case 'ENDORSMENT':
						$variableService->save(array('PREV_ENDORSMENT' => $encoded));
						break;
					case 'SCORE':
						$this->resultService->storeGrade($this->getCurrentDeliveryResult(),
														 $this->getCurrentActivityExecution(),
														 $suffix,
														 $encoded);
						break;
					case ANSWERED_VALUES_ID:
						foreach (json_decode($encoded, true) as $varIdentifier => $varValue) {
							if (!is_string($varValue)) {
								$varValue = json_encode($varValue);
							}
							$this->resultService->storeResponse(
								$this->getCurrentDeliveryResult(),
								$this->getCurrentActivityExecution(),
								$varIdentifier,
								$varValue
							);
						}
						break;
						
					default:
						common_Logger::w('No treatment of '.$suffix);
					break;
				}
			}
		}

        //save scores
        //save variables
	
        //disabling the call to the legacy ResultDelivery Service, TODO PPL update the configured result servers
		//parent::save();
		//
		echo json_encode(array('saved' => $saved));
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383B end
    }

    /**
     * trace the events generated by the delivery
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function traceEvents()
    {
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383D begin
        
	$saved = false;
		if($this->hasRequestParameter('token') && $this->hasRequestParameter('events')){
			$token = $this->getRequestParameter('token');
			if($this->authenticate($token)){
				
				//check if there is events
				$events = $this->getRequestParameter('events');
				if(is_array($events)){
					
					$executionEnvironment = $this->getExecutionEnvironment();
					
					//get the process execution uri
					if(isset($executionEnvironment[CLASS_PROCESS_EXECUTIONS]['uri'])){
					
						$processURI = $executionEnvironment[CLASS_PROCESS_EXECUTIONS]['uri'];
						$process_id = substr($processURI, strpos($processURI, '#') + 1);
						
						$eventService = tao_models_classes_EventsService::singleton();
					
						//get the event to be foltered on the server side
						$eventFilter = array();
						$compiledFolder = $this->getCompiledFolder($executionEnvironment);
						if(file_exists($compiledFolder .'events.xml')){
							$eventFilter = $eventService->getEventList($compiledFolder .'events.xml', 'server');
						}
					
						//trace the events
						$resultExt = common_ext_ExtensionsManager::singleton()->getExtensionById('taoResults');
						$saved = $eventService->traceEvent($events, $process_id, $resultExt->getConstant('EVENT_LOG_PATH'), $eventFilter);
					}
				}
			}
		}
		echo json_encode(array('saved' => $saved));
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383D end
    }

    /**
     * evaluate the user's answers on the server
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function evaluate()
    {
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383F begin
        //disabling the call to the legacy ResultDelivery Service, TODO PPL update the configured result servers
	//is now calling tao_actions_Api
	//parent::evaluate();
        $responses = json_decode($_POST['data']);

	$itemService = taoItems_models_classes_ItemsService::singleton();
	$outcomes = $itemService->evaluate($this->getCurrentItem(), $responses);
	foreach ($outcomes as $identifier => $value) {
			$this->resultService->storeGrade(
				$this->getCurrentDeliveryResult(),
				$this->getCurrentActivityExecution(),
				$identifier,
				$value["value"] //$outcome is an array with "identifier" and "value" sending the whole array makes the grade to contain two values "SCORE" and the actual score
			);
	    }
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383F end
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003844 begin
        //disabling the call to the legacy ResultDelivery Service, TODO PPL update the configured result servers
	//is now calling tao_actions_Api
	parent::__construct();
	
	$loader = new common_ext_ExtensionLoader(common_ext_ExtensionsManager::singleton()->getExtensionById('taoResults'));
	$loader->load();
        
	$this->resultService = taoResults_models_classes_ResultsService::singleton();
        
        // this test is worthless, since we can not progress if we don't have the executionEvironement in our Session
        if(!$this->hasRequestParameter('token')
        	|| !$this->authenticate($this->getRequestParameter('token'))) {
        		throw new taoDelivery_models_classes_SubjectException('Invalid Token'); 
		}
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003844 end
    }

    /**
     * Short description of method getCurrentActivityExecution
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    private function getCurrentActivityExecution()
    {
        $returnValue = null;

        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000384D begin
        
        // cost of current implementation: 1 query
        
        // since we are on the same server we can load the environment imediately from session
        $environment = $this->getExecutionEnvironment();
        
        $classProcessInstance = new core_kernel_classes_Resource($environment[CLASS_PROCESS_EXECUTIONS]['uri']);
        
        $returnValue = $classProcessInstance->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS));
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000384D end

        return $returnValue;
    }

    /**
     * Short description of method getCurrentItem
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    private function getCurrentItem()
    {
        $returnValue = null;

        // section 127-0-1-1--6c3f6a0b:13944999530:-8000:0000000000003B8A begin

        // cost of current implementation: 1 query
        
        // since we are on the same server we can load the environment imediately from session
        $environment = $this->getExecutionEnvironment();
		$returnValue = new core_kernel_classes_Resource($environment[TAO_ITEM_CLASS]['uri']);
        // section 127-0-1-1--6c3f6a0b:13944999530:-8000:0000000000003B8A end

        return $returnValue;
    }

    /**
     * Short description of method getCurrentDeliveryResult
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    private function getCurrentDeliveryResult()
    {
        $returnValue = null;

        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000384F begin
	    $environment = $this->getExecutionEnvironment();
        $classProcessInstance = new core_kernel_classes_Resource($environment[CLASS_PROCESS_EXECUTIONS]['uri']);
		
        if (common_cache_SessionCache::singleton()->contains(self::DELIVERYRESULT_SESSION_SERIAL)) {
        	$data = common_cache_SessionCache::singleton()->get(self::DELIVERYRESULT_SESSION_SERIAL);
        	if (isset($data['process']) && $data['process'] == $classProcessInstance->getUri()) {
	        	$returnValue = new core_kernel_classes_Resource($data['dr']);
        	} else {
				common_Logger::i('recovered Delivery Result does not match ProcessExecution');
        	}
        }
        if (is_null($returnValue)) {
        	// cost of current implementation: EXPENSIV SEARCH
	        $localNS = core_kernel_classes_Session::singleton()->getNameSpace();
	        $drClass = new core_kernel_classes_Class(TAO_DELIVERY_RESULT);
	        
	        $result = $drClass->searchInstances(array(
	        	PROPERTY_RESULT_OF_PROCESS	=> $classProcessInstance->getUri()
	        ));
	        
	        if (count($result) > 1) {
	        	throw new common_exception_Error('More then 1 deliveryResult for process '.$classProcessInstance);
	        } elseif (count($result) == 1) {
	        	$returnValue = array_shift($result);
				common_Logger::d('found Delivery Result after search for '.$classProcessInstance);
	        } else {
				// create Instance
				// since we are on the same server we can load the environment imediately from session
	        	$environment = $this->getExecutionEnvironment();
				$subject = new core_kernel_classes_Resource($environment[TAO_SUBJECT_CLASS]['uri']);
				$delivery = new core_kernel_classes_Resource($environment[TAO_DELIVERY_CLASS]['uri']);
				
				$label = $delivery->getLabel().' '.$subject->getLabel();
				$returnValue = $drClass->createInstanceWithProperties(array(
					RDFS_LABEL					=> $label,
					PROPERTY_RESULT_OF_PROCESS	=> $classProcessInstance,
					PROPERTY_RESULT_OF_DELIVERY => $delivery,
					PROPERTY_RESULT_OF_SUBJECT	=> $subject,
				));
				common_Logger::d('spawned Delivery Result for '.$classProcessInstance);
	        }
	        $data = array('process' => $classProcessInstance->getUri(), 'dr' => $returnValue->getUri());
	        common_cache_SessionCache::singleton()->put($data, self::DELIVERYRESULT_SESSION_SERIAL);
        }
    	
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000384F end

        return $returnValue;
    }

} /* end of class taoDelivery_actions_InternalResultServer */

?>