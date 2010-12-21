<?php
require_once ('tao/actions/Api.class.php');
include_once('taoResults/includes/constants.php');

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoResults
 * @subpackage action
 *
 */
class ResultDelivery extends Api {

	protected $resultService;
	
	public function __construct(){
		parent::__construct();
		
		$this->resultService = tao_models_classes_ServiceFactory::get('Results');
	}
	
	/**
	 * Initialize the item execution environment 
	 */
	public function initialize(){
		
		$executionEnvironment = array();
		
		if($this->hasRequestParameter('processUri') && 
				$this->hasRequestParameter('itemUri') && 
				$this->hasRequestParameter('testUri') &&
				$this->hasRequestParameter('deliveryUri') ){
			
			$user = $this->userService->subjectService->getCurrentUser();
			if(is_null($user)){
				throw new Exception(__('No user is logged in'));
			}
			
			$process	= new core_kernel_classes_Resource($this->getRequestParameter('processUri'));
			$item 		= new core_kernel_classes_Resource($this->getRequestParameter('itemUri'));
			$test 		= new core_kernel_classes_Resource($this->getRequestParameter('testUri'));
			$delivery 	= new core_kernel_classes_Resource($this->getRequestParameter('deliveryUri'));
			
			
			$executionEnvironment = $this->createExecutionEnvironment($process, $item, $test, $delivery, $user);
		
		}	
		echo json_encode($executionEnvironment);
	}
	
	/**
     * Evaluate user's reponses 
     * @todo Check if the data sent by the user are compliant with our standart (and secure) 
     * @public
     */
	public function evaluate () {
		$returnValue = array();
        
        if($this->hasRequestParameter('token') && $this->hasRequestParameter('data')){
            $token = $this->getRequestParameter('token');
            if($this->authenticate($token)){
            	
            	$executionEnvironment = $this->getExecutionEnvironment();
            	
            	$responses = json_decode($_POST['data']);
            	$item = new core_kernel_classes_Resource($executionEnvironment[TAO_ITEM_CLASS]['uri']);
            	
            	if(!is_null($responses) && !is_null($item)){
	            	$itemService = tao_models_classes_ServiceFactory::get('Items');
	            	$outcomes = $itemService->evaluate($item, $responses);
	            	
		            if(count($outcomes) > 0){
		            	$dtis = array(
							'TAO_PROCESS_EXEC_ID' 	=> $executionEnvironment[CLASS_PROCESS_EXECUTIONS]['uri'],
							'TAO_ITEM_ID' 			=> $executionEnvironment[TAO_ITEM_CLASS]['uri'],
							'TAO_TEST_ID' 			=> $executionEnvironment[TAO_TEST_CLASS]['uri'],
							'TAO_DELIVERY_ID' 		=> $executionEnvironment[TAO_DELIVERY_CLASS]['uri'],
							'TAO_SUBJECT_ID' 		=> $executionEnvironment[TAO_SUBJECT_CLASS]['uri']
						);
						$variables = array();
						foreach($outcomes as $outcome){
							$variables[$outcome['identifier']] = $outcome['value'];
						}
						if(count($variables) > 0){
							$this->resultService->addResultVariables($dtis, $variables, true);
						}
					}
	            }
            }
        }
        echo json_encode($returnValue);
	}
	
	/**
	 * save data pushed to the server
	 */
	public function save(){
		$saved = false;
		if($this->hasRequestParameter('token')){
			$token = $this->getRequestParameter('token');
			if($this->authenticate($token)){
				
				$executionEnvironment = $this->getExecutionEnvironment();
				
				$dtis = array(
					'TAO_PROCESS_EXEC_ID' 	=> $executionEnvironment[CLASS_PROCESS_EXECUTIONS]['uri'],
					'TAO_ITEM_ID' 			=> $executionEnvironment[TAO_ITEM_CLASS]['uri'],
					'TAO_TEST_ID' 			=> $executionEnvironment[TAO_TEST_CLASS]['uri'],
					'TAO_DELIVERY_ID' 		=> $executionEnvironment[TAO_DELIVERY_CLASS]['uri'],
					'TAO_SUBJECT_ID' 		=> $executionEnvironment[TAO_SUBJECT_CLASS]['uri']
				);
				
				if($this->hasRequestParameter('taoVars')){
					
					$resultNS = $executionEnvironment['localNamespace'];
					
					//here we save the TAO variables
					$taoVars = array();
					foreach($this->getRequestParameter('taoVars') as $key => $value){
						$taoVars[str_replace($resultNS.'#', '', $key)] = $value;
					}
					$this->resultService->addResultVariables($dtis, $taoVars, true);
					
				}
				if($this->hasRequestParameter('userVars')){
					//here we save the user variables
					$this->resultService->addResultVariables($dtis, $this->getRequestParamter('userVars'), false);
				}
				
				$saved = true;
			}
		}
		
		echo json_encode(array('saved' => $saved));
	} 
	
	/**
	 * Get the list of events regarding the events file in the item 
	 */
	public function getEvents(){
		$events = array();
		if($this->hasRequestParameter('token')){
			$token = $this->getRequestParameter('token');
			if($this->authenticate($token)){
				
				$compiledFolder = $this->getCompiledFolder($this->getExecutionEnvironment());
				if(file_exists($compiledFolder .'events.xml')){
					$eventService = tao_models_classes_ServiceFactory::get("tao_models_classes_EventsService");
					$events = $eventService->getEventList($compiledFolder .'events.xml');
				}
			}
		}
		echo json_encode($events);
	}
	
	/**
	 * trace the sent events 
	 */
	public function traceEvents(){
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
						
						$eventService = tao_models_classes_ServiceFactory::get('tao_models_classes_EventsService');
					
						//get the event to be foltered on the server side
						$eventFilter = array();
						$compiledFolder = $this->getCompiledFolder($executionEnvironment);
						if(file_exists($compiledFolder .'events.xml')){
							$eventFilter = $eventService->getEventList($compiledFolder .'events.xml', 'server');
						}
						//var_dump($events, $process_id, EVENT_LOG_PATH, $eventFilter);
					
						//trace the events
						$saved = $eventService->traceEvent($events, $process_id, EVENT_LOG_PATH, $eventFilter);
					}
				}
			}
		}
		echo json_encode(array('saved' => $saved));
	}
}
?>