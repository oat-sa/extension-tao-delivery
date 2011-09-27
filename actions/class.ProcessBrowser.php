<?php
error_reporting(E_ALL);

class taoDelivery_actions_ProcessBrowser extends wfEngine_actions_WfModule{
	
	protected $processExecution = null;
	protected $activityExecution = null;
	protected $processExecutionService = null;
	protected $activityExecutionService = null;
	protected $requestedActivityDefinition = null;
	
	public function __construct(){
		
		parent::__construct();
		
		$this->processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$this->activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		
		//validate all posted values:
		
		$processExecutionUri = urldecode($this->getRequestParameter('processUri'));
		
		
		if(!empty($processExecutionUri)){
			
			$processExecution = new core_kernel_classes_Resource($processExecutionUri);
			//check that the process execution is not finished or closed here:
			if($this->processExecutionService->isFinished($processExecution)){
				
				//cannot browse a finished process execution:
				$this->redirectToMain();
				
			}else{
				
				$this->processExecution = $processExecution;
				
				$activityUri = urldecode($this->getRequestParameter('activityUri'));
				$activityExecutionUri = urldecode($this->getRequestParameter('activityExecutionUri'));
				
				if(!empty($activityUri)){
					$this->requestedActivityDefinition = new core_kernel_classes_Resource($activityUri);
				}
				
				if(!empty($activityExecutionUri)){
					
					$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
					$currentActivityExecutions = $this->processExecutionService->getCurrentActivityExecutions($this->processExecution);
					
					//check if it is a current activity exec:
					if(array_key_exists($activityExecutionUri, $currentActivityExecutions)){
						
						$this->activityExecution = $activityExecution;

						//if ok, check the nonce:
						$nc = $this->getRequestParameter('nc');
						if($this->activityExecutionService->checkNonce($this->activityExecution, $nc)){
							$this->activityExecutionNonce = true;
						}else{
							$this->activityExecutionNonce = false;
						}
						
					}
				}
			}
		}
		
	}
	
	protected function redirectToIndex($activityUri = ''){
		
		$parameters = array();
		if(!empty($activityUri)){
			$parameters['activityUri'] = $activityUri;
		}
		
		if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
			$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
		}else{
			$this->index($parameters['activityUri']);
		}
		
	}
	
	protected function redirectToMain(){
		Session::removeAttribute("processUri");
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
	}
	
	public function index($activityUri = ''){
		
		
		if(is_null($this->processExecution)){
			$this->redirectToMain();
			return;
		}
		if(empty($activityUri) && !is_null($this->requestedActivityDefinition)){
			$activityUri = $this->requestedActivityDefinition->uriResource;
		}
		
		/*
		 * known use of Session::setAttribute("processUri") in:
		 * - taoDelivery_actions_ItemDelivery::runner()
		 * - tao_actions_Api::createAuthEnvironment()
		 * TODO: clean usage
		 */
		Session::setAttribute("processUri", $this->processExecution->uriResource);
		
		//init services:
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$interactiveServiceService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_InteractiveServiceService');
		
		//get current user:
		$currentUser = $userService->getCurrentUser();
		if(is_null($currentUser)){
			throw new wfEngine_models_classes_ProcessExecutionException("No current user found!");
		}
		
		//get activity execution from currently available process definitions:
		$currentlyAvailableActivityDefinitions = $this->processExecutionService->getAvailableCurrentActivityDefinitions($this->processExecution, $currentUser, true);
		
		//get a valid activity execution from the given activity definition
		$activityExecution = null;
		if(count($currentlyAvailableActivityDefinitions) == 0){
			//no available current activity definition found: no permission or issue in process execution:
			$this->pause();
			return;
		}else{
			if(!empty($activityUri)){
				foreach($currentlyAvailableActivityDefinitions as $availableActivity){
					if($availableActivity->uriResource == $activityUri){
						$activityExecution = $this->processExecutionService->initCurrentActivityExecution($this->processExecution, new core_kernel_classes_Resource($activityUri), $currentUser);
						break;
					}
				}
				if(is_null($activityExecution)){
					//invalid activity definition requested:
					$this->requestedActivityDefinition = null;
//					$invalidActivity = new core_kernel_classes_Resource($activityUri);
//					throw new wfEngine_models_classes_ProcessExecutionException("invalid choice of activity definition in process browser {$invalidActivity->getLabel()} ({$invalidActivity->uriResource}). \n<br/> The link may be outdated.");
					$this->redirectToIndex();
					return;
				}
			}else{
				if(count($currentlyAvailableActivityDefinitions) == 1){
					$activityExecution = $this->processExecutionService->initCurrentActivityExecution($this->processExecution, array_pop($currentlyAvailableActivityDefinitions), $currentUser);
					if(is_null($activityExecution)){
						throw new wfEngine_models_classes_ProcessExecutionException('cannot initiate the actiivty execution of the unique next activity definition');
					}
				}else{
					
					//count > 1:
					//parallel branch, ask the user to select activity to execute:
					$this->pause();
					return;
				}
			}
		}
		
		//user data for browser view
		$userViewData = UsersHelper::buildCurrentUserForView(); 
		$this->setData('userViewData', $userViewData);
		$browserViewData = array(); // general data for browser view.
		
		if(!is_null($activityExecution)){
			
			$browserViewData['activityExecutionUri']= $activityExecution->uriResource;
			$browserViewData['activityExecutionNonce']= $this->activityExecutionService->getNonce($activityExecution);
			Session::setAttribute('activityExecutionUri', $activityExecution->uriResource);//for variable service only?
			
			//get interactive services (call of services):
			$activityDefinition = $this->activityExecutionService->getExecutionOf($activityExecution);
			$interactiveServices = $activityService->getInteractiveServices($activityDefinition);
			$services = array();
			foreach($interactiveServices as $interactiveService){
				$services[] = array(
					'callUrl'	=> $interactiveServiceService->getCallUrl($interactiveService, $activityExecution),
					'style'		=> $interactiveServiceService->getStyle($interactiveService),
					'resource'	=> $interactiveService,
				);
			}
			$this->setData('services', $services);
			
			//set activity control:
			$controls = $activityService->getControls($activityDefinition);
			$browserViewData['controls'] = array(
				'backward' 	=> in_array(INSTANCE_CONTROL_BACKWARD, $controls),
				'forward'	=> in_array(INSTANCE_CONTROL_FORWARD, $controls)
			);
		
			// If paused, resume it:
			if ($this->processExecutionService->isFinished($this->processExecution)){
				$this->processExecutionService->resume($this->processExecution);
			}
			
			//get process definition:
			$processDefinition = $this->processExecutionService->getExecutionOf($this->processExecution);
			
			// Browser view main data.
			$browserViewData['processLabel'] 			= $processDefinition->getLabel();
			$browserViewData['processExecutionLabel']	= $this->processExecution->getLabel();
			$browserViewData['activityLabel'] 			= $activityDefinition->getLabel();
			$browserViewData['processUri']				= $this->processExecution->uriResource;
			$browserViewData['active_Resource']			="'".$activityDefinition->uriResource."'" ;
			$browserViewData['isInteractiveService'] 	= true;
			$this->setData('browserViewData', $browserViewData);
					
			$this->setData('activity', $activityDefinition);
		
			/* <DEBUG> :populate the debug widget */
			if(DEBUG_MODE){
				
				$this->setData('debugWidget', DEBUG_MODE);
				
				$servicesResources = array();
				foreach($services as $service){
					$servicesResources[] = array(
						'resource' => $service['resource'],
						'callUrl'	=> $service['callUrl'],
						'style'	=> $service['style'],
						'input'		=> $interactiveServiceService->getInputValues($interactiveService, $activityExecution),
						'output'	=> $interactiveServiceService->getOutputValues($interactiveService, $activityExecution)
					);
				}
				
				$this->setData('debugData', array(
						'Activity' => $activityDefinition,
						'ActivityExecution' => $activityExecution,
						'CurrentActivities' => $currentlyAvailableActivityDefinitions,
						'Services' => $servicesResources,
						'VariableStack' => wfEngine_models_classes_VariableService::getAll()
				));
			}
			/* </DEBUG> */

			$this->setView('process_browser.tpl');
		}
	}

	public function back(){
		
		if(is_null($this->processExecution) || is_null($this->activityExecution) || !$this->activityExecutionNonce){
			$this->redirectToIndex();
			return;
		}
		
		$previousActivityDefinitions = $this->processExecutionService->performBackwardTransition($this->processExecution, $this->activityExecution);
		
		//reinitiate nonce:
		$this->activityExecutionService->createNonce($this->activityExecution);
		
		if($this->processExecutionService->isPaused($this->processExecution)){
			$this->pause();
		}else{
			$activityUri = '';
			if(count($previousActivityDefinitions) == 1){
				$activityUri = urlencode(array_pop($previousActivityDefinitions)->uriResource);
			}
			$this->redirectToIndex($activityUri);
		}
	}

	public function next(){
		
		if(is_null($this->processExecution) || is_null($this->activityExecution) || !$this->activityExecutionNonce){
			$this->redirectToIndex();
			return;
		}
		
		$nextActivityDefinitions = $this->processExecutionService->performTransition($this->processExecution, $this->activityExecution);
		
		//reinitiate nonce:
		$this->activityExecutionService->createNonce($this->activityExecution);
		
		if($this->processExecutionService->isFinished($this->processExecution)){
			$this->redirectToMain();
		}
		elseif($this->processExecutionService->isPaused($this->processExecution)){
			$this->pause();
		}
		else{
			//if $nextActivityDefinitions count = 1, pass it to the url:
			$activityUri = '';
			if(count($nextActivityDefinitions) == 1){
				$activityUri = urlencode(array_pop($nextActivityDefinitions)->uriResource);
			}
			$this->redirectToIndex($activityUri);
		}
	}

	public function pause(){
		
		if(!is_null($this->processExecution)){
			if(!$this->processExecutionService->isPaused($this->processExecution)){
				$this->processExecutionService->pause($this->processExecution);
			}
		}
		
		$this->redirectToMain();
	}
	
	public function loading(){
		$this->setView('itemLoading.tpl');
	}

}
?>
