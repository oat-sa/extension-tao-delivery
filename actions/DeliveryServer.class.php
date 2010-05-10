<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * DeliveryServer Controller
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
	
	/**
     * default action: set the view to enable the user to log into the deliveyr server.
	 * if the user is identified, redirection to deliveryIndex
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void
     */
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

				$this->redirect(tao_helpers_Uri::url('deliveryIndex', 'DeliveryServer'));
			}
		}

		$this->setView('deliveryServer.tpl');
	}
	
	/**
     * Check if a subject is in session and return it. If not, redirection to index
	 *
     * @access private
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return void or core_kernel_class_Resource
     */
	private function isSubjectSession(){
		$subject = $_SESSION["subject"];
		if(is_null($subject) && !($subject instanceof core_kernel_classes_Resource)){
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
		}else{
			return $subject;
		}
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
		$var_subjectLabel = $this->service->getProcessVariable("subjectLabel");
		$var_wsdl = $this->service->getProcessVariable("wsdlContract");
		if(!is_null($var_subjectUri) && !is_null($var_wsdl) && !is_null($var_subjectLabel)){
			$processExecutionFactory->variables = array(
			$var_subjectUri->uriResource => $subject->uriResource,
			$var_subjectLabel->uriResource => $subject->getLabel(),
			$var_wsdl->uriResource => $wsdlContract
			);//no need to encode here, will be donce in Service::getUrlCall
		}else{
			throw new Exception('one of the required process variables is missing: "subjectUri", "subjectLabel" and/or "wsdlContract"');
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

		$visibleProcess =$this->service->getDeliveries($subject,false);

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

		//$availableProcessDefinition = $processClass->getInstances();
		$availableProcessDefinition = $this->service->getDeliveries($subject);


		$this->setData('availableProcessDefinition',$availableProcessDefinition);
		$this->setData('processViewData',$processViewData);
		$this->setView('deliveryIndex.tpl');
	}

	/**
	 * Logout, destroy the session and back to the login page
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return
	 */
	public function logout(){
		unset($_SESSION['taoqual.authenticated']);
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
	}
	
	public function resultUploadWsdl(){
		$pathToResultServer= ROOT_URL.'/taoDelivery/views/deliveryServer/resultServer';

		$wsdl='
		<?xml version="1.0" encoding="UTF-8"?>
		<wsdl:definitions
			name="tao"
			targetNamespace="urn:tao"
			xmlns="http://schemas.xmlsoap.org/wsdl/"
			xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
			xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
			xmlns:si="http://soapinterop.org/xsd"
			xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
			xmlns:tns="urn:tao"
			xmlns:typens="urn:tao"
			xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
			xmlns:xsd="http://www.w3.org/2001/XMLSchema"
			xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<wsdl:types>
				<xsd:schema
					targetNamespace="urn:tao"
					xmlns="http://schemas.xmlsoap.org/wsdl/"
					xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
					xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
					xmlns:si="http://soapinterop.org/xsd"
					xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
					xmlns:tns="urn:tao"
					xmlns:typens="urn:tao"
					xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
					xmlns:xsd="http://www.w3.org/2001/XMLSchema"
					xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
					<xsd:complexType name="ArrayOfstring">
						<xsd:complexContent>
							<xsd:restriction base="SOAP-ENC:Array">
								<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="xsd:string[]"/>
							</xsd:restriction>
						</xsd:complexContent>
					</xsd:complexType>
				  
				</xsd:schema>
			</wsdl:types>
		<wsdl:message name="setResultRequest">
		<wsdl:part name="pResultDS" type="tns:ArrayOfstring" /> 
		<wsdl:part name="pResultID" type="tns:ArrayOfstring" /> 
		<wsdl:part name="pResultSQ" type="tns:ArrayOfstring" /> 
		<wsdl:part name="pResultNB" type="tns:ArrayOfstring" /> 
		</wsdl:message>
		   
			<wsdl:message name="setResultResponse">
				<wsdl:part name="pResult" type="tns:ArrayOfstring"/>
			</wsdl:message>

		<wsdl:message name="isFullyOkRequest">
		<wsdl:part name="IDresult" type="tns:ArrayOfstring" /> 
		<wsdl:part name="numberElts" type="tns:ArrayOfstring" /> 
		</wsdl:message>
		   
			<wsdl:message name="isFullyOkResponse">
				<wsdl:part name="pResult" type="tns:ArrayOfstring"/>
			</wsdl:message>

			<wsdl:portType name="TAO_PortType">
				<wsdl:operation name="setResult">
					<documentation>Request to connect to the TAO system</documentation>
					<wsdl:input message="tns:setResultRequest"/>
					<wsdl:output message="tns:setResultResponse"/>
				</wsdl:operation>
				<wsdl:operation name="isFullyOk">
					<documentation>Request to connect to the TAO system</documentation>
					<wsdl:input message="tns:isFullyOkRequest"/>
					<wsdl:output message="tns:isFullyOkResponse"/>
				</wsdl:operation>
			</wsdl:portType>

		<wsdl:binding name="TAO_Binding" type="tns:TAO_PortType">
				<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>

				<wsdl:operation name="setResult">
					<soap:operation
					   soapAction="'.$pathToResultServer.'/Uploadresultserver.php"
						style="rpc"/>
					<wsdl:input>
						<soap:body
							encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
							namespace="urn:tao"
							use="encoded"/>
					</wsdl:input>
					<wsdl:output>
						<soap:body
							encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
							namespace="urn:tao"
							use="encoded"/>
					</wsdl:output>
				</wsdl:operation>
				
				<wsdl:operation name="isFullyOk">
					<soap:operation
					   soapAction="'.$pathToResultServer.'/uploadResultServer.php"
						style="rpc"/>
					<wsdl:input>
						<soap:body
							encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
							namespace="urn:tao"
							use="encoded"/>
					</wsdl:input>
					<wsdl:output>
						<soap:body
							encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
							namespace="urn:tao"
							use="encoded"/>
					</wsdl:output>
				</wsdl:operation>

			</wsdl:binding>
			<wsdl:service name="Uploadresult">
				<wsdl:port binding="tns:TAO_Binding" name="tao_UploadresultPort">
					<soap:address location="'.$pathToResultServer.'/uploadResultServer.php"/>
				</wsdl:port>
			</wsdl:service>

		</wsdl:definitions>';

		echo $wsdl;
	}

}
?>