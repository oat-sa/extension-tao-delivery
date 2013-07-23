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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
 
/**
 * This class is a container to call TAO XHTML items.
 * 
 * It enables you to run this kind of item in the context of a TAO server
 * by initiliazing it (set the context varaibles) and by rendering it.   
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_actions_ItemDeliveryService extends taoDelivery_actions_DeliveryApi {
	
	public function index(){

		$session = PHPSession::singleton();
		
		if($session->hasAttribute('processUri') && 
				$this->hasRequestParameter('itemUri') && 
				$this->hasRequestParameter('testUri') &&
				$this->hasRequestParameter('deliveryUri') ){
			
			$user = $this->userService->getCurrentUser();
			if(is_null($user)){
				throw new Exception(__('No user is logged in'));
			}
			$lang = core_kernel_classes_Session::singleton()->getDataLanguage();
			
			$process	= new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getSessionAttribute('processUri')));
			$item 		= new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('itemUri')));
			$test 		= new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('testUri')));
		
			if(preg_match("/^http/", $this->getRequestParameter('deliveryUri'))){
				$delivery 	= new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('deliveryUri')));
			}else{
				$deliveryParams = @unserialize(urldecode($this->getRequestParameter('deliveryUri')));
				if($deliveryParams === false){
					throw new Exception(__("Wrong delivery uri"));
				}
				if(is_array($deliveryParams) && count($deliveryParams) > 0){
					$delivery 	= new core_kernel_classes_Resource(tao_helpers_Uri::decode($deliveryParams[0]));
				}
				else{
					throw new Exception(__("Unable to load the  delivery"));
				}
			}
	
			//$executionEnvironment = $this->createExecutionEnvironment($process, $item, $test, $delivery, $user);
			
			$serial = $this->getSessionAttribute('activityExecutionUri');
			$variableData = taoDelivery_models_classes_itemVariables_VariableProxy::singleton()->get($user, $serial);
			$this->setData('storageData', array(
				'serial'	=> $serial,
				'data'		=> is_null($variableData) ? array() : $variableData
			));
			
			$this->setData('itemPath', taoDelivery_helpers_ItemAccessControl::getAccessUrl($delivery, $test, $item, $lang));
			
			$this->setView('itemDelivery/item_runner.tpl');			
			
		}
	}
	
	public function get() {
		$provider = new taoDelivery_models_classes_itemAccess_ActionAccessProvider();
		$filename = $provider->decodeUrl($_SERVER['REQUEST_URI']);
		if (file_exists($filename)) {
			$mimeType = tao_helpers_File::getMimeType($filename);
			header('Content-Type: '.$mimeType);
			$fp = fopen($filename, 'rb');
 			fpassthru($fp);
		} else {
			throw new tao_models_classes_FileNotFoundException($filename);
		}
	}
	
	public function saveVariables() {
	    $user = new core_kernel_classes_Resource(core_kernel_classes_Session::singleton()->getUserUri());
        $success = taoDelivery_models_classes_itemVariables_VariableProxy::singleton()->set(
            $user,
            $this->getRequestParameter('id'),
            $this->getRequestParameter('data')
        );
		echo json_encode($success);
	}
}
?>
