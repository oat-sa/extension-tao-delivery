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

class DeliveryServer extends DeliveryServerModule{

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
	public function index()
	{
		
		$userService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService');
		$subject = $userService->getCurrentUser();

		$wfEngine = $_SESSION["WfEngine"];
		$login = $_SESSION['taoqual.userId'];
		$this->setData('login',$login);
		
		$processes 			= $wfEngine->getProcessExecutions();
		
		//init required services
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		//get current user:
		$currentUser = $userService->getCurrentUser();
		
		//init variable that save data to be used in the view
		$processViewData 	= array();

		$uiLanguages		= I18nUtil::getAvailableLanguages();
		$this->setData('uiLanguages',$uiLanguages);
		
		//get the definition of delivery available for the subject:
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

					foreach ($proc->currentActivity as $currentActivity){
						$activity = $currentActivity;
						
						$isAllowed = $activityExecutionService->checkAcl($activity->resource, $currentUser);
						$currentActivities[] = array(
							'label'				=> $currentActivity->label,
							'uri' 				=> $currentActivity->uri,
							'may_participate'	=> (!$proc->isFinished() && $isAllowed),
							'finished'			=> $proc->isFinished(),
							'allowed'			=> $isAllowed
						);

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
	
	public function resultUploadWsdl(){
		$pathToResultServer= ROOT_URL.'/taoDelivery/views/resultServer';

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