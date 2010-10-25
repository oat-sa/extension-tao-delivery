<?php 
require_once('tao/actions/CommonModule.class.php');

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
class ItemDelivery extends CommonModule {
	
	/**
	 * Name of the variable used for the execution environment 
	 * @var string
	 */
	const ENV_VAR_NAME = 'taoEnv';
	
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
		
		if($this->hasRequestParameter('processUri') && $this->hasRequestParameter('itemUri')){
			
			$subjectService = tao_models_classes_ServiceFactory::get("tao_models_classes_UserService");
			$user = $subjectService->getCurrentUser();
			if(is_null($user)){
				throw new Exception(__('No user\'s logged in'));
			}
			
			$process	= new core_kernel_classes_Resource($this->getRequestParameter('processUri'));
			$item 		= new core_kernel_classes_Resource($this->getRequestParameter('itemUri'));
			$test 		= new core_kernel_classes_Resource($this->getRequestParameter('testUri'));
			$delivery 	= new core_kernel_classes_Resource($this->getRequestParameter('deliveryUri'));
			
			//get the sum of a unique token to identify the content
			$token = sha1( uniqid(self::ENV_VAR_NAME, true) );		//the env var is just used as a SALT
			
			//we build the data to give to the item
			$executionEnvironment = array(

				'token'			=> $token,
				'localNamspace' => core_kernel_classes_Session::singleton()->getNameSpace(),
			
				CLASS_PROCESS_EXECUTIONS => array(
					'uri'		=> $process->uriResource,
					RDFS_LABEL	=> $process->getLabel()
				),
				
				TAO_ITEM_CLASS	=> array(
					'uri'		=> $item->uriResource,
					RDFS_LABEL	=> $item->getLabel()
				),
				TAO_TEST_CLASS	=> array(
					'uri'		=> $test->uriResource,
					RDFS_LABEL	=> $test->getLabel()
				),
				TAO_DELIVERY_CLASS	=> array(
					'uri'		=> $delivery->uriResource,
					RDFS_LABEL	=> $delivery->getLabel()
				),
				TAO_SUBJECT_CLASS => array(
					'uri'					=> $user->uriResource,
					RDFS_LABEL				=> $user->getLabel(),
					PROPERTY_USER_LOGIN		=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN)),
					PROPERTY_USER_FIRTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_FIRTNAME)),
					PROPERTY_USER_LASTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LASTNAME))
				)
			);
			
			Session::setAttribute(self::ENV_VAR_NAME.'_'.$user->uriResource, $executionEnvironment);
			
			//retrieving of the compiled item content
			$testFolder = substr($test->uriResource, strpos($test->uriResource, '#') + 1);
			$itemFolder = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
			$compiled = BASE_PATH. "/compiled/{$testFolder}/{$itemFolder}/index.html"; 

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