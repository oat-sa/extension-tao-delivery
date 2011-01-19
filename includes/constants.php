<?php
//include constants for the wfEngine:
include_once ROOT_PATH . '/wfEngine/includes/constants.php';
include_once ROOT_PATH . '/tao/includes/constants.php';
include_once ROOT_PATH . '/taoItems/includes/constants.php';
include_once ROOT_PATH . '/taoTests/includes/constants.php';
include_once ROOT_PATH . '/taoResults/includes/constants.php';


//define specific constants to delivery extension:
$todefine = array(
	'TAO_ITEM_MODEL_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	
	'TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects',

	'TAO_DELIVERY_COMPILED_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled',
	'TAO_DELIVERY_CAMPAIGN_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign',	
	'TAO_DELIVERY_CAMPAIGN_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign',
	
	'TAO_DELIVERY_RESULTSERVER_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer',	
	'TAO_DELIVERY_RESULTSERVER_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer',
	'TAO_DELIVERY_DEFAULT_RESULT_SERVER' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1267866417009087900',
	
	'TAO_DELIVERY_RESULTSERVER_RESULT_URL_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#saveResultUrl',
	'TAO_DELIVERY_RESULTSERVER_EVENT_URL_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#saveEventUrl',
	'TAO_DELIVERY_RESULTSERVER_MATCHING_URL_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#matchingUrl',
	'TAO_DELIVERY_RESULTSERVER_MATCHING_SERVER_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#matchingServerSide',

	'TAO_DELIVERY_HISTORY_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History',	
	'TAO_DELIVERY_HISTORY_SUBJECT_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject',
	'TAO_DELIVERY_HISTORY_DELIVERY_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery',
	'TAO_DELIVERY_HISTORY_TIMESTAMP_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp',
	
	'TAO_DELIVERY_MAXEXEC_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#MaxExec',
	
	'TAO_DELIVERY_AUTHORINGMODE_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode',
	'TAO_DELIVERY_SIMPLEMODE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811802',
	'TAO_DELIVERY_ADVANCEDMODE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811803',
	
	'TAO_DELIVERY_DELIVERYCONTENT'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent',
	'TAO_DELIVERY_PROCESS'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryProcess',
	
	'TAO_GROUP_DELIVERIES_PROP'	=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries',
	
	'INSTANCE_FORMALPARAM_TESTURI' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1288956260043375900',
	'INSTANCE_SERVICEDEFINITION_TESTCONTAINER' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceTestContainer'
);

foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);

?>
