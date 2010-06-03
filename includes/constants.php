<?php
//include constants for the wfEngine:
include_once ROOT_PATH . '/wfEngine/includes/constants.php';

//define specific constants to delivery extension:
$todefine = array(
	'TAO_OBJECT_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject',
	'TAO_DELIVERY_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery',
	'TAO_SUBJECT_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_GROUP_CLASS' => 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'TAO_TEST_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'TAO_ITEM_MODEL_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	'TAO_RESULT_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#Result',
	'TEST_RELATED_ITEMS_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems',
	'TEST_TESTCONTENT_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	'TEST_COMPILED_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#compiled',
	'TEST_ACTIVE_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#active',
	'ITEM_ITEMCONTENT_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent',
	'ITEM_ITEMMODEL_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel',
	'ITEM_MODEL_RUNTIME_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile', 
	'SUBJECT_LOGIN_PROP' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Login',
	'SUBJECT_PASSWORD_PROP' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Password',
	
	'TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects',
	'TAO_DELIVERY_TESTS_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Tests',
	
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_MODEL_WATERPHENIX'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#i125933161031263',
	'TAO_ITEM_MODEL_CAMPUS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#campus',
	'TAO_ITEM_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',

	'TAO_ITEM_AUTHORING_BASE_URI' 		=> ROOT_PATH.'/taoItems/data',
	'TAO_ITEM_HAWAI_TPL_FILE'	 		=> ROOT_PATH.'/taoItems/data/black_ref.xml',
	'TAO_ITEM_CAMPUS_TPL_FILE'	 		=> ROOT_PATH.'/taoItems/data/campus_ref.xml',

	'GENERIS_BOOLEAN'		=> 'http://www.tao.lu/Ontologies/generis.rdf#Boolean',
	'RDFS_TYPE'				=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
	
	'TAO_DELIVERY_COMPILED_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled',
	'TAO_DELIVERY_CAMPAIGN_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign',	
	'TAO_DELIVERY_CAMPAIGN_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign',
	'TAO_DELIVERY_RESULTSERVER_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer',	
	'TAO_DELIVERY_RESULTSERVER_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer',
	'TAO_DELIVERY_RESULTSERVER_URL_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl',
	'TAO_DELIVERY_DEFAULT_RESULT_SERVER' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1267866417009087900',
	'TAO_DELIVERY_HISTORY_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History',	
	'TAO_DELIVERY_HISTORY_SUBJECT_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject',
	'TAO_DELIVERY_HISTORY_DELIVERY_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery',
	'TAO_DELIVERY_HISTORY_TIMESTAMP_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp',
	'TAO_DELIVERY_DELIVERYCONTENT'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent',
	'TAO_DELIVERY_MAXEXEC_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#MaxExec',
	'TAO_DELIVERY_AUTHORINGMODE_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode',
	'TAO_DELIVERY_SIMPLEMODE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811802',
	'TAO_DELIVERY_ADVANCEDMODE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811803'
);

foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);

?>
