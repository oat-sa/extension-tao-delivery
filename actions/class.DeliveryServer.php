<?php
/**
 * DeliveryServer Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class taoDelivery_actions_DeliveryServer extends taoDelivery_actions_DeliveryServerModule{

	/**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct(){

		parent::__construct();
		$this->service = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryServerService');
	}
		
	/**
     * Instanciate a process instance from a process definition
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param processDefinitionUri
     * @return void
     */
	public function processAuthoring($processDefinitionUri)
	{
		
		$userService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService');
		$subject = $userService->getCurrentUser();
		
		$processDefinitionUri = urldecode($processDefinitionUri);
		$delivery = taoDelivery_models_classes_DeliveryAuthoringService::getDeliveryFromProcess(new core_kernel_classes_Resource($processDefinitionUri), true);
		if(is_null($delivery)){
			throw new Exception("no delivery found for the selected process definition");
		}

		$wsdlContract = $this->service->getResultServer($delivery);
		if(empty($wsdlContract)){
			throw new Exception("no wsdl contract found for the current delivery");
		}

		ini_set('max_execution_time', 200);

		$processExecutionFactory = new wfEngine_models_classes_ProcessExecutionFactory();
			
		$processExecutionFactory->name = $delivery->getLabel();
		if(strlen(trim($processExecutionFactory->name)) == 0){
			$deliveryService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryService');
			$processExecutionFactory->name = "Execution ".count($deliveryService->getHistory($delivery))." of ".$delivery->getLabel();
		}
		$processExecutionFactory->comment = 'Created in delivery server on' . date(DATE_ISO8601);
			
		$processExecutionFactory->execution = $processDefinitionUri;
			
		$var_delivery = new core_kernel_classes_Resource(INSTANCE_PROCESSVARIABLE_DELIVERY);
		if(wfEngine_helpers_ProcessUtil::checkType($var_delivery, new core_kernel_classes_Class(CLASS_PROCESSVARIABLES))){
			$processExecutionFactory->variables = array($var_delivery->uriResource => $delivery->uriResource);//no need to encode here, will be donce in Service::getUrlCall
		}else{
			throw new Exception('the required process variable "delivery" is missing in delivery server, reinstalling tao is required');
		}

		$newProcessExecution = $processExecutionFactory->create();


		$newProcessExecution->feed();


		$processUri = urlencode($newProcessExecution->uri);



		//add history of delivery execution in the delivery ontology
		$this->service->addHistory($delivery, $subject);

		$param = array( 'processUri' => urlencode($processUri));
		$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $param));
	}
	
	/**
     * Set a view with the list of process instances (both started or finished) and available process definitions
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param processDefinitionUri
     * @return void
     */
	public function index()
	{
		
		$userService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService');
		$subject = $userService->getCurrentUser();

		$login = $_SESSION['taoqual.userId'];
		$this->setData('login',$login);
		
		$wfEngineService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_WfEngineService');
		
		//$processes 			= $wfEngineService->getProcessExecutions();
		$processes = array();
		
		//init required services
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		//get current user:
		$currentUser = $userService->getCurrentUser();
	
		// Get the activities where the user has an active token.
		$tokenClass = new core_kernel_classes_Class(CLASS_TOKEN);
		$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$processExecutionClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);

		$currentUserTokens = $tokenClass->searchInstances(array(PROPERTY_TOKEN_CURRENTUSER => $currentUser->uriResource));
		foreach ($currentUserTokens as $token) {
			$activityExecution = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITYEXECUTION));
			$processExecution = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION));
			$processes[] = new wfEngine_models_classes_ProcessExecution($processExecution->uriResource);
		}
		
		//init variable that save data to be used in the view
		$processViewData 	= array();

		$uiLanguages		= tao_helpers_I18n::getAvailableLangs();
		$this->setData('uiLanguages',$uiLanguages);
		
		//get the definition of delivery available for the subject:
		$visibleProcess = $this->service->getDeliveries($subject,false);
				
		foreach ($processes as $proc)
		{
			$type 	= $proc->process->resource->getLabel();
			$label 	= $proc->resource->getLabel();
			$uri 	= $proc->uri;
			$status = $proc->status;
			$persid	= "-";

			$executionOfProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
			$res = $proc->resource->getOnePropertyValue($executionOfProp);
			if($res !=null && $res instanceof core_kernel_classes_Resource){
				$defUri = $res->uriResource;

					
				if(in_array($defUri,$visibleProcess)){

						
					$currentActivities = array();
					
					$isAllowed = false;
					foreach ($proc->currentActivity as $currentActivity){
						$activity = $currentActivity;
						
						$isAllowed = $activityExecutionService->checkAcl($activity->resource, $currentUser, $proc->resource);
						
						$currentActivities[] = array(
							'label'				=> $currentActivity->resource->getLabel(),
							'uri' 				=> $currentActivity->uri,
							'may_participate'	=> (!$proc->isFinished() && $isAllowed),
							'finished'			=> $proc->isFinished(),
							'allowed'			=> $isAllowed
						);

					}
					
					//ondelivery server, display only user's delivery (finished and paused): ($proc->currentActivity is empty or checkACL returns "false")
					if(!$isAllowed){
						continue;
					}
						
					$processViewData[] = array(
						'type' 			=> $type,
						'label' 		=> $label,
						'uri' 			=> $uri,
						'persid'		=> $persid,
						'activities'	=> $currentActivities,
						'status'		=> $status
					);
				}
			}

		}
		$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
		
		//get deliveries for the current user (set in groups extension)
		$availableProcessDefinitions = $this->service->getDeliveries($subject);

		//filter process that can be initialized by the current user (2nd check...)
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$authorizedProcessDefinitions = array();
		foreach($availableProcessDefinitions as $processDefinition){
			if($processExecutionService->checkAcl($processDefinition, $currentUser)){
				$authorizedProcessDefinitions[] = $processDefinition;
			}
		}
		
		$this->setData('availableProcessDefinition',$authorizedProcessDefinitions);
		$this->setData('processViewData',$processViewData);
		$this->setView('deliveryIndex.tpl');
	}
}
?>