<?php 
require_once('tao/actions/Api.class.php');

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
class ItemDelivery extends Api {
	
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
		
		if($this->hasRequestParameter('processUri') && 
				$this->hasRequestParameter('itemUri') && 
				$this->hasRequestParameter('testUri') &&
				$this->hasRequestParameter('deliveryUri') ){
			
			$user = $this->userService->getCurrentUser();
			if(is_null($user)){
				throw new Exception(__('No user is logged in'));
			}
			
			$process	= new core_kernel_classes_Resource($this->getRequestParameter('processUri'));
			$item 		= new core_kernel_classes_Resource($this->getRequestParameter('itemUri'));
			$test 		= new core_kernel_classes_Resource($this->getRequestParameter('testUri'));
			$delivery 	= new core_kernel_classes_Resource($this->getRequestParameter('deliveryUri'));
			
			$executionEnvironment = $this->createExecutionEnvironment($process, $item, $test, $delivery, $user);
			
			//retrieving of the compiled item content
			$deliveryFolder = substr($delivery->uriResource, strpos($delivery->uriResource, '#') + 1);
			$itemFolder = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
			$compiled = BASE_PATH. "/compiled/{$testFolder}/{$itemFolder}/index.html"; 
			
//			$compiled = ROOT_PATH."/taoItems/views/runtime/i1288014658084751100/index.html";
			if(!file_exists($compiled)){
				throw new Exception(__("Unable to load the compiled item content"));
			}
			
			try{
				$doc = new DOMDocument();
				$doc->loadHTMLFile($compiled);
				
				//injecting the data directly in the item
				$clientCode = 'var '.self::ENV_VAR_NAME.' = '.json_encode($executionEnvironment).';';
				$scriptElt = $doc->createElement('script', $clientCode);
				$scriptElt->setAttribute('type', 'text/javascript');
				
				$headNodes = $doc->getElementsByTagName('head');
				
				foreach($headNodes as $headNode){
					$scriptNodes = $headNode->getElementsByTagName('script');
					if($scriptNodes->length > 0){
						$headNode->insertBefore($scriptElt, $scriptNodes->item(0));
					}
					else{
						$headNode->appendChild($scriptElt);
					}
					break;
				}
				
				//render the item
				echo $doc->saveHTML();
				
			}
			catch(DOMException $de){
				error_log($de->getMessage);		//log the error in the log file and display a common message
				throw new Exception(__("An error occured while loading the item"));
			}
		}
	}
}
?>