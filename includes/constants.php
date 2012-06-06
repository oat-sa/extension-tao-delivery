<?php
//define specific constants to delivery extension:
$todefine = array(
	'TAO_ITEM_MODEL_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	
	'TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects',

	'TAO_DELIVERY_COMPILED_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled',
	'TAO_DELIVERY_CAMPAIGN_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign',	
	'TAO_DELIVERY_CAMPAIGN_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign',
	
	'TAO_DELIVERY_RESULTSERVER_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer',	
	'TAO_DELIVERY_RESULTSERVER_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer',
	'TAO_DELIVERY_DEFAULT_RESULT_SERVER' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DualResultServer',
	
	'TAO_DELIVERY_RESULTSERVER_RESULT_URL_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#saveResultUrl',
	'TAO_DELIVERY_RESULTSERVER_EVENT_URL_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#saveEventUrl',
	'TAO_DELIVERY_RESULTSERVER_MATCHING_URL_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#matchingUrl',
	'TAO_DELIVERY_RESULTSERVER_MATCHING_SERVER_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#matchingServerSide',

	'TAO_DELIVERY_HISTORY_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History',	
	'TAO_DELIVERY_HISTORY_SUBJECT_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject',
	'TAO_DELIVERY_HISTORY_DELIVERY_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery',
	'TAO_DELIVERY_HISTORY_TIMESTAMP_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp',
	'TAO_DELIVERY_HISTORY_PROCESS_INSTANCE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryProcessInstance',
    
	'TAO_DELIVERY_MAXEXEC_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec',
	'TAO_DELIVERY_START_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart',
	'TAO_DELIVERY_END_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd',
	
	'TAO_DELIVERY_AUTHORINGMODE_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode',
	'TAO_DELIVERY_SIMPLEMODE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringModeSimple',
	'TAO_DELIVERY_ADVANCEDMODE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringModeAdvanced',
	
	'TAO_DELIVERY_DELIVERYCONTENT'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent',
	'TAO_DELIVERY_PROCESS'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryProcess',
	
	'TAO_GROUP_DELIVERIES_PROP'	=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries',
	
	'INSTANCE_FORMALPARAM_TESTURI' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#FormalParamTestUri',
	'INSTANCE_SERVICEDEFINITION_TESTCONTAINER' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceTestContainer',
	'INSTANCE_PROCESSVARIABLE_DELIVERY' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ProcessVarDelivery',
	
	// Coding
	'TAO_DELIVERY_CODINGMETHODE_PROP'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCodingMethode',
	'TAO_DELIVERY_CODINGMETHODE_AUTOMATED'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingMethodeAutomated',
	'TAO_DELIVERY_CODINGMETHODE_MANUAL'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingMethodeManual',

	'TAO_DELIVERY_CODINGSTATUS_PROP'			=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCodingStatus',
	'TAO_DELIVERY_CODINGSTATUS_GRADING'			=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusGrading',
	'TAO_DELIVERY_CODINGSTATUS_CONCILIATION'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusConciliation',
	'TAO_DELIVERY_CODINGSTATUS_COMMITED'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusCommited',

	'TAO_DELIVERY_ACTIVE_PROP'				=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#active',

);
?>
