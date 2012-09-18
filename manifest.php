<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'taoDelivery',
	'description' => 'TAO http://www.tao.lu',
	'version' => '2.3',
	'author' => 'CRP Henri Tudor',
	'dependencies' => array('wfEngine'),
	'models' => array('http://www.tao.lu/Ontologies/TAODelivery.rdf',
		'http://www.tao.lu/Ontologies/taoFuncACL.rdf'),
	'install' => array(
		'rdf' => array(
				array('ns' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf', 'file' => dirname(__FILE__). '/models/ontology/taodelivery.rdf'),
				array('ns' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf', 'file' => dirname(__FILE__). '/models/ontology/coding.rdf'),
				array('ns' => 'http://www.tao.lu/Ontologies/taoFuncACL.rdf', 'file' => dirname(__FILE__). '/models/ontology/aclrole.rdf')
		)
	),
	'classLoaderPackages' => array(
		dirname(__FILE__).'/actions/',
		dirname(__FILE__).'/helpers/'
	),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# models directory
		"DIR_MODELS"			=> $extpath."models".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# helpers directory
		"DIR_HELPERS"			=> $extpath."helpers".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'DeliveryServerAuthentification',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath . 'taoDelivery',
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . '/taoDelivery',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . '/taoDelivery/views/',
	 
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . '/tao/views/',
		'TAOVIEW_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	
		#PROCESS BASE WWW the web path of the process authoring tool
		'PROCESS_BASE_WWW'		=> ROOT_URL. '/wfEngine/views/',
		'PROCESS_SCRIPT_URL'	=> ROOT_URL. '/wfEngine/views/js/authoring/',
		'PROCESS_BASE_PATH'		=> ROOT_PATH.DIRECTORY_SEPARATOR.'wfEngine'.DIRECTORY_SEPARATOR,
		'PROCESS_TPL_PATH'		=> ROOT_PATH.DIRECTORY_SEPARATOR.'wfEngine'.DIRECTORY_SEPARATOR
									.'views'.DIRECTORY_SEPARATOR
									.'templates'.DIRECTORY_SEPARATOR
									.'authoring'.DIRECTORY_SEPARATOR,
	
		# Next/Previous button usable or not.
		'USE_NEXT'				=> true,
		'USE_PREVIOUS'			=> true,
		'FORCE_NEXT'			=> true,
									
		'TEST_COMPILATION_TIME'	=> 300,
	)
);
?>