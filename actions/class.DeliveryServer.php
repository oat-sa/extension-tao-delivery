<?php
/**  
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

/**
 * DeliveryServer Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class taoDelivery_actions_DeliveryServer extends tao_actions_CommonModule
{

	/**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct(){
		if(!$this->_isAllowed()){
	        $this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification', 'taoDelivery', array('errorMessage' => urlencode(__('Access denied. Please renew your authentication!')))));
		}
		$this->service = taoDelivery_models_classes_DeliveryExecutionService::singleton();
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
		
		$label = core_kernel_classes_Session::singleton()->getUserLabel();
		$this->setData('login',$label);
		
		/*
		//init required services
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
		$userService = taoDelivery_models_classes_UserService::singleton();

		//get current user:
		$subject = $userService->getCurrentUser();
                
		//init variable that save data to be used in the view
		$processViewData 	= array();
		
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
				
				if(in_array($processDefinition->getUri(), $visibleProcess)){
					
					$currentActivities = array();
					
					// Bypass ACL Check if possible...
					if ($status->getUri() == INSTANCE_PROCESSSTATUS_FINISHED) {
						$processViewData[] = array(
							'type' 			=> $processDefinition->getLabel(),
							'label' 		=> $processExecution->getLabel(),
							'uri' 			=> $processExecution->getUri(),
							'activities'	=> array(array('label' => '', 'uri' => '', 'may_participate' => false, 'finished' => true, 'allowed'=> true)),
							'status'		=> $status
						);
						continue;
						
					}else{
						
						$isAllowed = false;
						$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processExecution);
						foreach ($currentActivityExecutions as $uri => $currentActivityExecution){
							$isAllowed = $activityExecutionService->checkAcl($currentActivityExecution, $subject, $processExecution);
							$currentActivity = $activityExecutionService->getExecutionOf($currentActivityExecution);
							$currentActivities[] = array(
								'label'				=> $currentActivity->getLabel(),
								'uri' 				=> $uri,
								'may_participate'	=> ($status->getUri() != INSTANCE_PROCESSSTATUS_FINISHED && $isAllowed),
								'finished'			=> ($status->getUri() == INSTANCE_PROCESSSTATUS_FINISHED),
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
							'uri' 			=> $processExecution->getUri(),
							'activities'	=> $currentActivities,
							'status'		=> $status
						);
						
					}
				}
			}
		}
		*/
		//get deliveries for the current user (set in groups extension)
		$userUri = core_kernel_classes_Session::singleton()->getUserUri();
		$deliveries = is_null($userUri) ? array() : $this->service->getAvailableDeliveries($userUri);

		
		$this->setData('availableDeliveries', $deliveries);
		$this->setData('processViewData', array());
		$this->setView('runtime/index.tpl');
	}
	
	public function initDeliveryExecution() {
	    $compiledDelivery = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
	   
		$callUrl = taoDelivery_models_classes_CompilationService::singleton()->getRuntimeCallUrl($compiledDelivery);
		//$deliveryExecution = $this->service->initDeliveryExecution($compiledDelivery);

        $this->initResultServer($compiledDelivery, $callUrl);

		$this->setData('serviceCallUrl', $callUrl);
		//$this->setData('serviceCallId', $deliveryExecution->getUri());
		
	    $this->setData('userLabel', core_kernel_classes_Session::singleton()->getUserLabel());
		//$this->setData('compiled', $delivery);
	    $this->setView('deliveryExecution.tpl');
	}
    /**
     * intialize the result server using the delivery configuration and for this results session submission
     * @param compiledDelviery
     */

    private function initResultServer($compiledDelivery, $executionIdentifier) {
        $resultServerCallOverride =  $this->hasRequestParameter('resultServerCallOverride') ? $this->getRequestParameter('resultServerCallOverride') : false;
         if (!($resultServerCallOverride)) {
            taoDelivery_models_classes_DeliveryExecutionService::singleton()->initResultServer($compiledDelivery, $executionIdentifier);
        }
    }
	
	private function wf() {
	    
	    $callUrl = $interactiveServiceService->getCallUrl($interactiveService, $activityExecution);
	    if (in_array(substr($callUrl, -1), array('?', '&'))) {
	        $callUrl .= 'standalone=true';
	    } else {
	        $callUrl .= (strpos($callUrl, '?') ? '&' : '?').'standalone=true';
	    }
	    $services[] = array(
	        'callUrl'	=> $callUrl,
	        'style'		=> $interactiveServiceService->getStyle($interactiveService),
	        'resource'	=> $interactiveService,
	    );
	}
	
}