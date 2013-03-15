<?php
/*  
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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
//define specific constants to delivery extension:
$todefine = array(
	'TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects',

	'TAO_DELIVERY_COMPILED_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled',
	'TAO_DELIVERY_CAMPAIGN_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign',	
	'TAO_DELIVERY_CAMPAIGN_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign',
	
	'TAO_DELIVERY_RESULTSERVER_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer',	
	'TAO_DELIVERY_RESULTSERVER_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer',
	'TAO_DELIVERY_DEFAULT_RESULT_SERVER' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#LocalResultServer',
	
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
	'TAO_DELIVERY_CODINGMETHOD_PROP'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCodingMethode',
	'TAO_DELIVERY_CODINGMETHOD_AUTOMATED'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingMethodeAutomated',
	'TAO_DELIVERY_CODINGMETHOD_MANUAL'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingMethodeManual',

	'TAO_DELIVERY_CODINGSTATUS_PROP'			=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCodingStatus',
	'TAO_DELIVERY_CODINGSTATUS_GRADING'			=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusGrading',
	'TAO_DELIVERY_CODINGSTATUS_CONCILIATION'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusConciliation',
	'TAO_DELIVERY_CODINGSTATUS_COMMITED'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusCommited',

	'TAO_DELIVERY_ACTIVE_PROP'				=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#active',

);
?>
