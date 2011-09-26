<?php
error_reporting(E_ALL);

class taoDelivery_actions_ProcessBrowser extends wfEngine_actions_WfModule{
	
	protected $processExecution = null;
	protected $activityExecution = null;
	protected $processExecutionService = null;
	protected $activityExecutionService = null;
	
	public function __construct(){
		
		parent::__construct();
		
		$this->processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$this->activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		
	}
	
	protected function validateParameters(){
		
		$returnValue = true;
		
		$processExecutionUri = urldecode($this->getRequestParameter('processUri'));
		$activityExecutionUri = urldecode($this->getRequestParameter('activityExecutionUri'));
		if(empty($processExecutionUri)){
			Session::removeAttribute("processUri");
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
			$returnValue = false;
		}else{
			$processExecution = new core_kernel_classes_Resource($processExecutionUri);
			
			//check that the process execution is not finished or closed here:
			
			$this->processExecution = $processExecution;
			if(!empty($activityExecutionUri)){
				$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
				$currentActivityExecutions = $this->processExecutionService->getCurrentActivityExecutions($this->processExecution);
				//check if it is a current activity exec:
				if(array_key_exists($activityExecutionUri, $currentActivityExecutions)){
					$this->activityExecution = $activityExecution;
					
					//if ok, check the nonce:
					$nc = $this->getRequestParameter('nc');
					if($this->activityExecutionService->checkNonce($this->activityExecution, $nc)){
						$returnValue = true;
					}else{
						$this->redirectToIndex();
						$returnValue = false;
					}
				}else{
					//if not redirect to the process browser and let it manage the situation:
					$this->redirectToIndex();
					$returnValue = false;
				}
			}
		}
		
		return $returnValue;
	}
	
	protected function redirectToIndex($activityUri = ''){
		
		$parameters = array();
		$parameters['processUri'] = urlencode($this->processExecution->uriResource);
		$parameters['activityUri'] = '';
		if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
			$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
		}else{
			$this->index($parameters['processUri'], $parameters['activityUri']);
		}
		
	}
	
	public function index($processUri, $activityUri=''){
		/*
		 * known use of Session::setAttribute("processUri") in:
		 * - taoDelivery_actions_ItemDelivery::runner()
		 * - tao_actions_Api::createAuthEnvironment()
		 * TODO: clean usage
		 */
		Session::setAttribute("processUri", $processUri);//actually used somewhere...
		$activityUri = urldecode($activityUri);
		$processUri = urldecode($processUri); // parameters clean-up.
		$this->setData('processUri', $processUri);
		$processExecution = new core_kernel_classes_Resource($processUri);
		
		//user data for browser view
		$userViewData = UsersHelper::buildCurrentUserForView(); 
		$this->setData('userViewData', $userViewData);
		$browserViewData = array(); // general data for browser view.
		
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
		$currentlyAvailableActivityDefinitions = $this->processExecutionService->getAvailableCurrentActivityDefinitions($processExecution, $currentUser, true);
		
		$activityExecution = null;
		if(count($currentlyAvailableActivityDefinitions) == 0){
			//no available current activity definition found: no permission or issue in process execution:
			$this->pause(urlencode($processExecution->uriResource));
			return;
		}else{
			if(!empty($activityUri)){
				foreach($currentlyAvailableActivityDefinitions as $availableActivity){
					if($availableActivity->uriResource == $activityUri){
						$activityExecution = $this->processExecutionService->initCurrentActivityExecution($processExecution, new core_kernel_classes_Resource($activityUri), $currentUser);
						break;
					}
				}
				if(is_null($activityExecution)){
					//invalid choice of activity definition:
//					$invalidActivity = new core_kernel_classes_Resource($activityUri);
//					throw new wfEngine_models_classes_ProcessExecutionException("invalid choice of activity definition in process browser {$invalidActivity->getLabel()} ({$invalidActivity->uriResource}). \n<br/> The link may be outdated.");
					$this->index(urlencode($processExecution->uriResource));
					return;
				}
			}else{
				if(count($currentlyAvailableActivityDefinitions) == 1){
					$activityExecution = $this->processExecutionService->initCurrentActivityExecution($processExecution, array_pop($currentlyAvailableActivityDefinitions), $currentUser);
					if(is_null($activityExecution)){
						throw new wfEngine_models_classes_ProcessExecutionException('cannot initiate the actiivty execution of the unique next activity definition');
					}
				}else{
					//count > 1:
					//parallel branch, ask the user to select activity to execute:
					$this->pause(urlencode($processExecution->uriResource));
					return;
				}
			}
		}
		
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
			
			$processDefinition = $this->processExecutionService->getExecutionOf($processExecution);
			
			//set activity control:
			$controls = $activityService->getControls($activityDefinition);
			$browserViewData['controls'] = array(
				'backward' 	=> in_array(INSTANCE_CONTROL_BACKWARD, $controls),
				'forward'	=> in_array(INSTANCE_CONTROL_FORWARD, $controls)
			);
		
			// If paused, resume it:
			if ($this->processExecutionService->isFinished($processExecution)){
				$this->processExecutionService->resume($processExecution);
			}
			
			// Browser view main data.
			$browserViewData['processLabel'] 			= $processDefinition->getLabel();
			$browserViewData['processExecutionLabel']	= $processExecution->getLabel();
			$browserViewData['activityLabel'] 			= $activityDefinition->getLabel();
			$browserViewData['processUri']				= $processExecution->uriResource;
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

	public function back($processUri, $activityExecutionUri){
		
		if(!$this->validateParameters()){
			$this->redirectToIndex();
			return;
		}
		
		$processExecution = new core_kernel_classes_Resource(urldecode($processUri));
		$activityExecution = new core_kernel_classes_Resource(urldecode($activityExecutionUri));
		
		$previousActivityDefinitions = $this->processExecutionService->performBackwardTransition($processExecution, $activityExecution);
		
		//reinitiate nonce:
		$this->activityExecutionService->createNonce($activityExecution);
		
		if($this->processExecutionService->isPaused($processExecution)){
			$this->pause($processExecution->uriResource);
		}else{
			$parameters = array();
			$parameters['processUri'] = urlencode($processExecution->uriResource);
			if(count($previousActivityDefinitions) == 1){
				$parameters['activityUri'] = urlencode(array_pop($previousActivityDefinitions)->uriResource);
			}else{
				$parameters['activityUri'] = '';
			}
			
			if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
				$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
			}else{
				$this->index($parameters['processUri'], $parameters['activityUri']);
			}
			
		}
	}

	public function next($processUri, $activityExecutionUri){
		
		if(!$this->validateParameters()){
			$this->redirectToIndex();
			return;
		}
		
		$processExecution = new core_kernel_classes_Resource(urldecode($processUri));
		$activityExecution = new core_kernel_classes_Resource(urldecode($activityExecutionUri));
		
		$nextActivityDefinitions = $this->processExecutionService->performTransition($processExecution, $activityExecution);
		
		//reinitiate nonce:
		$this->activityExecutionService->createNonce($activityExecution);
		
		if($this->processExecutionService->isFinished($processExecution)){
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
		}
		elseif($this->processExecutionService->isPaused($processExecution)){
			$this->pause($processExecution->uriResource);
		}
		else{
			//if $nextActivityDefinitions count = 1, pass it to the url:
			$parameters = array();
			$parameters['processUri'] = urlencode($processExecution->uriResource);
			if(count($nextActivityDefinitions) == 1){
				$parameters['activityUri'] = urlencode(array_pop($nextActivityDefinitions)->uriResource);
			}else{
				$parameters['activityUri'] = '';
			}
			
			if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
				$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
			}else{
				$this->index($parameters['processUri'], $parameters['activityUri']);
			}
		}
	}

	public function pause($processUri){
		
		$processExecution = new core_kernel_classes_Resource(urldecode($processUri));
		if(!$this->processExecutionService->isPaused($processExecution)){
			$this->processExecutionService->pause($processExecution);
		}
		
		Session::removeAttribute("processUri");
//		$_SESSION["processUri"]= null;
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
		
	}
	
	public function loading(){
		$this->setView('itemLoading.tpl');
	}

}
?>
