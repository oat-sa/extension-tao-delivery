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
	public function initDeliveryExecution($processDefinitionUri){
		
		$userService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService');
		$deliveryAuthoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		
		$subject = $userService->getCurrentUser();
		
		$processDefinitionUri = urldecode($processDefinitionUri);
		$processDefinition = new core_kernel_classes_Resource($processDefinitionUri);
		$delivery = $deliveryAuthoringService->getDeliveryFromProcess($processDefinition, true);
		if(is_null($delivery)){
			throw new Exception("no delivery found for the selected process definition");
		}

		$wsdlContract = $this->service->getResultServer($delivery);
		if(empty($wsdlContract)){
			throw new Exception("no wsdl contract found for the current delivery");
		}

		ini_set('max_execution_time', 200);
		
		$processExecName = $delivery->getLabel();
		$processExecComment = 'Created in delivery server on' . date(DATE_ISO8601);
		$processVariables = array();
		$var_delivery = new core_kernel_classes_Resource(INSTANCE_PROCESSVARIABLE_DELIVERY);
		if($var_delivery->hasType(new core_kernel_classes_Class(CLASS_PROCESSVARIABLES))){
			$processVariables[$var_delivery->uriResource] = $delivery->uriResource;//no need to encode here, will be donce in Service::getUrlCall
		}else{
			throw new Exception('the required process variable "delivery" is missing in delivery server, reinstalling tao is required');
		}

		$newProcessExecution = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $processVariables);

		//add history of delivery execution in the delivery ontology
		$this->service->addHistory($delivery, $subject, $newProcessExecution);

		$param = array('processUri' => urlencode($newProcessExecution->uriResource));
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
	public function index(){
		
		//should be removed
		$login = $_SESSION['taoqual.userId'];
		$this->setData('login',$login);
		
		//init required services
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$userService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService');

		//get current user:
		$subject = $userService->getCurrentUser();
                
		//init variable that save data to be used in the view
		$processViewData 	= array();
		$uiLanguages		= tao_helpers_I18n::getAvailableLangs();
		$this->setData('uiLanguages', $uiLanguages);
		
		//get the definition of delivery available for the subject:
		$visibleProcess = $this->service->getDeliveries($subject,false);
		$processExecutions = $this->service->getStartedProcessExecutions($subject);
                
		foreach ($processExecutions as $processExecution){
			
			if(!is_null($processExecution) && $processExecution instanceof core_kernel_classes_Resource){
				
				$status = $processExecutionService->getStatus($processExecution);
				$processDefinition = $processExecutionService->getExecutionOf($processExecution);
				if(is_null($status) || !$status instanceof core_kernel_classes_Resource){
					continue;
				}
				
				if(in_array($processDefinition->uriResource, $visibleProcess)){
					
					$currentActivities = array();
					
					// Bypass ACL Check if possible...
					if ($status->uriResource == INSTANCE_PROCESSSTATUS_FINISHED) {
						$processViewData[] = array(
							'type' 			=> $processDefinition->getLabel(),
							'label' 		=> $processExecution->getLabel(),
							'uri' 			=> $processExecution->uriResource,
							'activities'	=> array(array('label' => '', 'uri' => '', 'may_participate' => false, 'finished' => true, 'allowed'=> true)),
							'status'		=> $status
						);
						continue;
						
					}else{
						
						$isAllowed = false;
						$availableCurrentActivities = $processExecutionService->getAvailableCurrentActivityDefinitions($processExecution, $subject);
						foreach ($availableCurrentActivities as $uri => $currentActivity){
							$isAllowed = $activityExecutionService->checkAcl($currentActivity, $subject, $processExecution);
							$currentActivities[] = array(
								'label'				=> $currentActivity->getLabel(),
								'uri' 				=> $uri,
								'may_participate'	=> ($status->uriResource != INSTANCE_PROCESSSTATUS_FINISHED && $isAllowed),
								'finished'			=> ($status->uriResource == INSTANCE_PROCESSSTATUS_FINISHED),
								'allowed'			=> $isAllowed
							);

						}

						//ondelivery server, display only user's delivery (finished and paused): ($processExecution->currentActivity is empty or checkACL returns "false")
						if(!$isAllowed){
							continue;
						}

						$processViewData[] = array(
							'type' 			=> $processDefinition->getLabel(),
							'label' 		=> $processExecution->getLabel(),
							'uri' 			=> $processExecution->uriResource,
							'activities'	=> $currentActivities,
							'status'		=> $status
						);
						
					}
				}
			}
		}
		
		//get deliveries for the current user (set in groups extension)
		$availableProcessDefinitions = $this->service->getDeliveries($subject);

		//filter process that can be initialized by the current user (2nd check...)
		$authorizedProcessDefinitions = array();
		foreach($availableProcessDefinitions as $processDefinition){
			if($processExecutionService->checkAcl($processDefinition, $subject)){
				$authorizedProcessDefinitions[] = $processDefinition;
			}
		}
		
		$this->setData('availableProcessDefinition', $authorizedProcessDefinitions);
		$this->setData('processViewData', $processViewData);
		$this->setView('deliveryIndex.tpl');
	}
}
?>