<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once dirname(__FILE__). '/../tao/includes/class.Bootstrap.php';


//use a different session name when we deliver a test
$modules = array(
	'DeliveryServerAuthentification', 
	'DeliveryServer', 
	'ProcessBrowser', 
	'ItemDelivery', 
	'ResultDelivery',
	'RecoveryContext'
);
$options = array();
foreach($modules as $module){
	if(tao_helpers_Request::contains('module', $module)){
		$options['session_name'] = 'TAO_TEST_SESSION';
		break;
	}
}

//need to load additional constants
$options['constants'] = array('taoTests', 'taoItems', 'taoResults');

$bootStrap = new BootStrap('taoDelivery', $options);
$bootStrap->start();
$bootStrap->dispatch();
?>