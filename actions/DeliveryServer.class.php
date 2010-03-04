<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Delivery Controller provide actions performed from url resolution
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class DeliveryServer extends Module{

	/**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct(){

		//log into generis:
		core_control_FrontController::connect(API_LOGIN, API_PASSWORD, DATABASE_NAME);

		$this->service = new taoDelivery_models_classes_DeliveryServerService();
	}

	public function index(){

		if(isset($_POST["login"]) && isset($_POST["password"])){
			$login = mysql_real_escape_string($_POST["login"]);
			$password = mysql_real_escape_string($_POST["password"]);
			$subject = $this->service->checkSubjectLogin($login, $password);

			if(is_null($subject)){
				$this->setData('login_message', __("wrong login or/and password,<br/> please try again"));
			}else{
				//fromthis point, the subject is identified (his/her role too)
				$_SESSION["subject"] = $subject;

				//goto next view: wfengine
				// header("location: /wfengine/");

				$_SESSION["WfEngine"] 		= WfEngine::singleton($login, $password);
				//		$_SESSION["userObject"] 	= WfEngine::singleton()->getUser();
				core_kernel_classes_Session::singleton()->setLg("EN");
					
				// Taoqual authentication and language markers.
				$_SESSION['taoqual.authenticated'] 		= true;
				$_SESSION['taoqual.lang']				= 'EN';
				$_SESSION['taoqual.serviceContentLang'] = 'EN';
				$_SESSION['taoqual.userId']				= $login;

				$this->redirect("../DeliveryServer/deliveryIndex");
			}
		}

		$this->setView('deliveryServer.tpl');
	}

	public function getDeliveries(core_kernel_classes_Resource $subject, $check = true){
		//get list of available deliveries for this subject:
		try{
			$deliveriesCollection = $this->service->getDeliveriesBySubject($subject->uriResource);
		}catch(Exception $e){
			echo "error: ".$e->getMessage();
		}

		$deliveries = array(
			'notCompiled' => array(),
			'noResultServer' => array(),
			'subjectExcluded' => array(),
			'wrongPeriod' => array(),
			'maxExecExceeded' => array(),
			'ok' => array()
		);

		foreach($deliveriesCollection->getIterator() as $delivery){

			if($check){

				//check if it is compiled:
				try{
					$isCompiled = $this->service->isCompiled($delivery);
				}catch(Exception $e){
					echo "error: ".$e->getMessage();
				}
				if(!$isCompiled){
					$deliveries['notCompiled'][] = $delivery;
					continue;
				}

				//check if it has valid resultServer defined:
				try{
					$resultServer = $this->service->getResultServer($delivery);

				}catch(Exception $e){
					echo "error: ".$e->getMessage();
				}
				if(empty($resultServer)){
					$deliveries['noResultServer'][] = $delivery;
					continue;
				}

				//check if the subject is excluded:
				try{
					$isExcluded = $this->service->isExcludedSubject($subject, $delivery);
				}catch(Exception $e){
					echo "error: ".$e->getMessage();
				}
				if($isExcluded){
					$deliveries['subjectExcluded'][] = $delivery;
					continue;
				}

				//check the period
				try{
					$isRightPeriod = $this->service->checkPeriod($delivery);
				}catch(Exception $e){
					echo "error$isRightPeriod: ".$e->getMessage();
				}
				if(!$isRightPeriod){
					$deliveries['wrongPeriod'][] = $delivery;
					continue;
				}

				//check maxexec: how many times the subject has already taken the current delivery?
				try{
					$historyCollection = $this->service->getHistory($delivery, $subject);
				}catch(Exception $e){
					echo "error: ".$e->getMessage();
				}
				if(!$historyCollection->isEmpty()){
					if($historyCollection->count() >= $this->service->getMaxExec($delivery) ){
						$deliveries['maxExecExceeded'][] = $delivery;
						continue;
					}
				}
					
			}//endif of "check"

			//all check performed:
			$deliveries['ok'][] = $delivery; //the process uri is contained in the property DeliveryContent of the delivery
		}

		$availableProcessDefinition = array();
		foreach($deliveries['ok'] as $availableDelivery){
			if($check) {
				$availableProcessDefinition[ $availableDelivery->uriResource ] = $availableDelivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
			}
			else{
				$res = $availableDelivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
				if($res !=null) {
					$availableProcessDefinition[ $availableDelivery->uriResource ] = $res->uriResource;
				}
			}
		}
		// var_dump($deliveries);

		//return this array to the workflow controller: extended from main
		return $availableProcessDefinition;
	}

	public function initDeliveryExecution(){
		//should be the first service of the first activity of the process, to be executed right after a process instanciation

		//get the process execution:
		$processInstance = null;

		//process instance -> process def -> delivery
		$delivery = taoDelivery_models_classes_DeliveryAuthoringService::getDeliveryFromProcess($processDefinition);
		$subject = $_SESSION["subject"];
		if(is_null($delivery)){
			throw new Exception("no delivery found for the selected process definition");
		}

		$wsdlContract = $this->service->getResultServer($delivery);
		if(empty($wsdlContract)){
			throw new Exception("no wsdl found for the current delivery");
		}

		//set the process variable values form the variables wsdl and subject (mandatory!)
		//use $processInstance->editPropertyValues( prop of process instance and instance of process var "wsdl location", get the wsdl url of the delivery  );
		//same for subjectUri

		//addhistory:
		$this->service->addHistory($delivery, $subject);

		//move on to the next activity:
	}

	private function isSubjectSession(){
		$subject = $_SESSION["subject"];
		if(is_null($subject) && !($subject instanceof core_kernel_classes_Resource)){
			$this->redirect('../DeliveryServer/');
		}else{
			return $subject;
		}
	}

	public function processAuthoring($processDefinitionUri)
	{

		$subject = $this->isSubjectSession();
		$processDefinitionUri = urldecode($processDefinitionUri);
		$delivery = taoDelivery_models_classes_DeliveryAuthoringService::getDeliveryFromProcess(new core_kernel_classes_Resource($processDefinitionUri));
		if(is_null($delivery)){
			throw new Exception("no delivery found for the selected process definition");
		}

		$wsdlContract = $this->service->getResultServer($delivery);
		if(empty($wsdlContract)){
			throw new Exception("no wsdl contract found for the current delivery");
		}

		ini_set('max_execution_time', 200);

		$processExecutionFactory = new ProcessExecutionFactory();
			
		$processExecutionFactory->name = $delivery->getLabel();
		$processExecutionFactory->comment = 'Created ' . date(DATE_ISO8601);
			
		$processExecutionFactory->execution = $processDefinitionUri;
			
		$var_subjectUri = $this->service->getProcessVariable("subjectUri");
		$var_wsdl = $this->service->getProcessVariable("wsdlContract");
		if(!is_null($var_subjectUri) && !is_null($var_wsdl)){
			$processExecutionFactory->variables = array(
			$var_subjectUri->uriResource => $subject->uriResource,
			$var_wsdl->uriResource => $wsdlContract
			);
		}else{
			throw new Exception('the required process variables "subjectUri" and/or "wsdlContract" waere not found');
		}

		$newProcessExecution = $processExecutionFactory->create();


		$newProcessExecution->feed();


		$processUri = urlencode($newProcessExecution->uri);



		//add history of delivery execution in the delivery ontology
		$this->service->addHistory($delivery, $subject);

		$param = array( 'processUri' => $processUri);
		$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser',$param));
	}

	public function deliveryIndex()
	{
		if (!isset($_SESSION['taoqual.authenticated'])){
			$this->redirect($this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer')));
		}

		$subject = $this->isSubjectSession();

		$wfEngine 			= $_SESSION["WfEngine"];
		$login = $_SESSION['taoqual.userId'];

		$this->setData('login',$login);
		$processes 			= $wfEngine->getProcessExecutions();



		$processViewData 	= array();

		$uiLanguages		= I18nUtil::getAvailableLanguages();
		$this->setData('uiLanguages',$uiLanguages);

		$visibleProcess =$this->getDeliveries($subject,false);

		foreach ($processes as $proc)
		{

			$type 	= $proc->process->label;
			$label 	= $proc->label;
			$uri 	= $proc->uri;
			$status = $proc->status;
			$persid	= "-";

			$executionOfProp = new core_kernel_classes_Property(EXECUTION_OF);
			$res = $proc->resource->getOnePropertyValue($executionOfProp);
			if($res !=null && $res instanceof core_kernel_classes_Resource){
				$defUri = $res->uriResource;

					
				if(in_array($defUri,$visibleProcess)){

						
					$currentActivities = array();

					foreach ($proc->currentActivity as $currentActivity)
					{
						$activity = $currentActivity;

						//if (UsersHelper::mayAccessProcess($proc->process))
						if (true)
						{
							$currentActivities[] = array('label' 			=> $currentActivity->label,
													 'uri' 				=> $currentActivity->uri,
													 'may_participate'	=> !$proc->isFinished());


						}
						$this->setData('currentActivities',$currentActivities);
					}

					if (true)
					{
						$processViewData[] = array('type' 		=> $type,
										  	   'label' 		=> $label,
											   'uri' 		=> $uri,
												'persid'	=> $persid,
										   	   'activities' => $currentActivities,
											   'status'		=> $status);


					}
				}
			}

		}
		$processClass = new core_kernel_classes_Class(CLASS_PROCESS);

		//		$availableProcessDefinition = $processClass->getInstances();
		$availableProcessDefinition = $this->getDeliveries($subject);


		$this->setData('availableProcessDefinition',$availableProcessDefinition);
		$this->setData('processViewData',$processViewData);
		$this->setView('deliveryIndex.tpl');
	}

	/**
	 * Logout, destroy the session and back to the login page
	 * @return
	 */
	public function logout(){
		unset($_SESSION['taoqual.authenticated']);
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
	}

}
?>