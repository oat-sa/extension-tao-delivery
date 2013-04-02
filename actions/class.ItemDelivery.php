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
?>
<?php 
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
class taoDelivery_actions_ItemDelivery extends taoDelivery_actions_DeliveryApi {
	
	/**
	 * @see ItemDelivery::runner
	 */
	public function index(){
		$this->forward(get_class($this), 'runner');
	}
	
	/**
	 * This action run an item during a delivery execution
	 * @return void
	 */
	public function runner(){
		$session = PHPSession::singleton();
		
		if($session->hasAttribute('processUri') && 
				$this->hasRequestParameter('itemUri') && 
				$this->hasRequestParameter('testUri') &&
				$this->hasRequestParameter('deliveryUri') ){
			
			$user = $this->userService->getCurrentUser();
			if(is_null($user)){
				throw new Exception(__('No user is logged in'));
			}
			
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
	
			$executionEnvironment = $this->createExecutionEnvironment($process, $item, $test, $delivery, $user);
			
			//retrieving of the compiled item content
			$compiledFolder = $this->getCompiledFolder($executionEnvironment);
			$compiled = $compiledFolder .'index.html';
			if(!file_exists($compiled)){
				if(DEBUG_MODE){
					echo "File: ".$compiled;
				}
				throw new Exception(__("Unable to load the compiled item content"));
			}
			
			$apis = array(
				'taoApi', 
				'taoMatching', 
				'wfApi'
			);
			
			$apis = array();
			
			// We inject the data directly in the item file
			try{
				$doc = new DOMDocument();
				$doc->loadHTMLFile($compiled);
				
				$initScriptElt = $doc->createElement('script');
				$initScriptElt->setAttribute('type', 'text/javascript');
				$initScriptElt->setAttribute('src', _url('initApis', 'ItemDelivery', 'taoDelivery', array('token' => $executionEnvironment['token'])));
				
				$headNodes = $doc->getElementsByTagName('head');
				
				foreach($headNodes as $headNode){
					$headNode->appendChild($initScriptElt);
				}
				
				//render the item
				echo $doc->saveHTML();
				
			}
			catch(DOMException $de){
				if(DEBUG_MODE){
					throw new Exception(__("An error occured while loading the item: ") . $de);
				}
				else{
					error_log($de->getMessage);		//log the error in the log file and display a common message
					throw new Exception(__("An error occured while loading the item"));
				}
			}
		}
	}
	
	/**
	 * Action to render a dynamic javascript page
	 * containing the APIs initialization for the current execution context
	 */
	public function initApis(){
		
		if($this->hasRequestParameter('token')){

			$executionEnvironment = $this->getExecutionEnvironment();
			if(isset($executionEnvironment['token'])){
				if($this->getRequestParameter('token') == $executionEnvironment['token'] && !empty($executionEnvironment['token'])){
				
					//get the deployment parameters
					$deploymentParams 	= array();
					$deliveryService 	= taoDelivery_models_classes_DeliveryService::singleton();
					$delivery 			= new core_kernel_classes_Resource($executionEnvironment[TAO_DELIVERY_CLASS]['uri']);
					$resultServer 		= $deliveryService->getResultServer($delivery);
					if(!is_null($resultServer)){
						$resultServerService 	= taoDelivery_models_classes_ResultServerAuthoringService::singleton();
						//$resultServer 			= new core_kernel_classes_Resource(TAO_DELIVERY_DEFAULT_RESULT_SERVER);
						$deploymentParams 		= $resultServerService->getDelpoymentParameters($resultServer);
					} else {
						common_Logger::w('No resultserver for delivery '.$delivery->getUri().' during initApis()', 'TAODELIVERY');
					}
					
					//response is a javascript stream 
					$this->setContentHeader('application/javascript');
					
					//initialize taoApi data source
					$this->setData('envVarName', self::ENV_VAR_NAME);
					$this->setData('executionEnvironment', json_encode($executionEnvironment));
					
					//initialize taoApi push parameters
					if(isset($deploymentParams['save_result_url'])){
						$this->setData('pushParams', json_encode(array(
								'url' 		=> $deploymentParams['save_result_url'], 
								'params'	=> array('token' => $executionEnvironment['token'])
						)));
					}
					
					//initialize taoApi events tracing parameters
					$compiledFolder = $this->getCompiledFolder($executionEnvironment);
					if(file_exists($compiledFolder .'/events.xml')){
						$eventService = tao_models_classes_EventsService::singleton();
						$eventData =  $eventService->getEventList($compiledFolder .'/events.xml');
						
						$this->setData('eventData', json_encode($eventData));
						$eventParams = 'null';
						if(isset($deploymentParams['save_event_url'])){
							$eventParams = json_encode(array(
								'url' 		=> $deploymentParams['save_event_url'], 
								'params'	=> array('token' => $executionEnvironment['token'])
							));
						}
						$this->setData('eventParams', $eventParams);
					}
					
					
					//initialize the matching Api
					if(isset($deploymentParams['matching_server'])){
						
						$this->setData('matchingServer', $deploymentParams['matching_server']);
						
						if($deploymentParams['matching_server'] === true){
							if(isset($deploymentParams['matching_url'])){
								$this->setData('matchingParams', json_encode( array(
									'url'		=> $deploymentParams['matching_url'],
									'params'	=> array('token' => $executionEnvironment['token']) 
								)));
							}
						}
						else{
							$itemService 	= taoItems_models_classes_ItemsService::singleton();
							$item			= new core_kernel_classes_Resource($executionEnvironment[TAO_ITEM_CLASS]['uri']);
							$this->setData('matchingData', json_encode($itemService->getMatchingData($item)));
						}
					}
					
					//initialize the wfApi recovery context
					$ctxParams = array('params' => array('token' => $executionEnvironment['token']));
					$ctxSourceParams = array_merge($ctxParams, array('url' => _url('retrieve', 'RecoveryContext')));
					$ctxDestParams = array_merge($ctxParams, array('url' => _url('save', 'RecoveryContext')));
					$this->setData('contextSourceParams', json_encode($ctxSourceParams));
					$this->setData('contextDestinationParams', json_encode($ctxDestParams));
					
					$this->setView('init_api.js.tpl');
				}
			}
		}
		return;
	}
}
?>
