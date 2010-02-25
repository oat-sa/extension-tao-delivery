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
 
class DeliveryServer{
	
	/**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new taoDelivery_models_classes_DeliveryServerService();
		$this->defaultData();
		
		// Session::setAttribute('currentSection', 'delivery');
	}
	
	public function login(){
		$login = $_POST["login"];
		$password = $_POST["password"];
		$subject = $this->service->checkSubjectLogin($login, $password);
		
		if(is_null($subject)){
			//return error message: "wrong login pass";	
		}
		
		//get list of available deliveries for this subject:
		$deliveriesCollection = $this->service->getDeliveriesBySubject($subject->uriResource);
		
		$deliveries = array(
			'notCompiled' => array(),
			'noResultServer' => array(),
			'subjectExcluded' => array(),
			'wrongPeriod' => array(),
			'maxExecExceeded' => array(),
			'ok' => array()
		);
		
		foreach($deliveriesCollection->getIterator() as $delivery){
		
			//check if it is compiled:
			$isCompiled = $this->service->isCompiled($delivery);
			if(!$isCompiled){
				$deliveries['notCompiled'][] = $delivery;
				continue;
			}
			
			//check if it has valid resultServer defined:
			$resultServer = $this->service->getResultServer($delivery);
			if(empty($resultServer)){
				$deliveries['noResultServer'][] = $delivery;
				continue;
			}
			
			//check if the subject is excluded:
			$isExcluded = $this->service->isExcludedSubject($subject, $delivery);
			if(!$isExcluded){
				$deliveries['subjectExcluded'][] = $delivery;
				continue;
			}
			
			//check the period
			$isRightPeriod = $this->service->checkPeriod($delivery);
			if(!$isRightPeriod){
				$deliveries['wrongPeriod'][] = $delivery;
				continue;
			}
			
			//check maxexec: how many times the subject has already taken the current delivery?
			$historyCollection = $this->service->getHistory($delivery, $subject); 
			if(!$historyCollection->isEmpty()){
				if($historyCollection->count() >= $this->service->getMaxExec($delivery)){
					$deliveries['maxExecExceeded'][] = $delivery;
				}
			}
			
			//all check performed:
			$deliveries['ok'][] = $delivery; //the process uri is contained in the property DeliveryContent of the delivery
		}
		
		$availableProcessDefinition = array();
		foreach($deliveries['ok'] as $availableDelivery){
			$availableProcessDefinition[ $availableDelivery->uriResource ] = $availableDelivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		}
		//return this array to the workflow controller: extended from main
		
	}
	
	public function initDeliveryExecution(){
		//should be the first service of the first activity of the process, to be executed right after a process instanciation
		
		//get the process execution:
		$processInstance = null;
		
		//set the process variable values form the variables wsdl and subject (mandatory!)
		//use $processInstance->editPropertyValues( prop of process instance and instance of process var "wsdl location", get the wsdl url of the delivery  );
		//same for subjectUri
		
		//addhistory:
		$this->service->addHistory($delivery, $subject);
		
		//move on to the next activity: 
	}

}
?>