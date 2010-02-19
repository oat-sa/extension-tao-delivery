<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');
require_once('taoDelivery/helpers/class.Precompilator.php');

/**
 * Delivery Controller provide actions performed from url resolution
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class Delivery extends TaoModule {
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Delivery');
		$this->defaultData();
		
		Session::setAttribute('currentSection', 'delivery');
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected delivery from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $delivery
	 */
	private function getCurrentDelivery(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$delivery = $this->service->getDelivery($uri, 'uri', $clazz);
		if(is_null($delivery)){
			throw new Exception("No delivery found for the uri {$uri}");
		}
		
		return $delivery;
	}
	
	/**
	 * @see TaoModule::getCurrentInstance
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentInstance(){
		return $this->getCurrentDelivery();
	}
	
	/**
	 * @see TaoModule::getRootClass
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getDeliveryClass();
	}
	
/*
 * controller actions
 */
	/**
	 * Render json data to populate the delivery tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getDeliveries(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$highlightUri = '';
		// if($this->hasSessionAttribute("showNodeUri")){
			// $highlightUri = $this->getSessionAttribute("showNodeUri");
			// unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		// } 
		$filter = '';
		if($this->hasRequestParameter('filter')){
			$filter = $this->getRequestParameter('filter');
		}
		
		// $handle = fopen("/deliverytree.txt","wb");
		// $fileContent = fwrite($handle, json_encode( $this->service->toTree( $this->service->getDeliveryClass(), true, true, $highlightUri, $filter)));
		// fclose($handle);
		
		echo json_encode( $this->service->toTree( $this->service->getDeliveryClass(), true, true, $highlightUri, $filter));
	}
	
	/**
	 * Edit a delivery class
	 * @see tao_helpers_form_GenerisFormFactory::classEditor
	 * @return void
	 */
	public function editDeliveryClass(){
		$clazz = $this->getCurrentClass();
		$myForm = $this->editClass($clazz, $this->service->getDeliveryClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Delivery Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit delivery class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Edit a delviery instance
	 * @see tao_helpers_form_GenerisFormFactory::instanceEditor
	 * @return void
	 */
	public function editDelivery(){
		$clazz = $this->getCurrentClass();
		$delivery = $this->getCurrentDelivery();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $delivery);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$delivery = $this->service->bindProperties($delivery, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($delivery->uriResource));
				$this->setData('message', __('Delivery saved'));
				$this->setData('reload', true);
			}
		}
		
		//get the campaign(s) related to this delivery
		$relatedCampaigns = $this->service->getRelatedCampaigns($delivery);
		$relatedCampaigns = array_map("tao_helpers_Uri::encode", $relatedCampaigns);
		$this->setData('relatedCampaigns', json_encode($relatedCampaigns));
		
		//get the subjects related to the test(s) of the current delivery!	
		$excludedSubjects = $this->service->getExcludedSubjects($delivery);
		$excludedSubjects = array_map("tao_helpers_Uri::encode", $excludedSubjects);
		$this->setData('excludedSubjects', json_encode($excludedSubjects));
		
		// $handle = fopen("/formRender.html","wb");
		// $content = fwrite($handle,$myForm->render());
		// fclose($handle);
		
		$this->setData('uri', tao_helpers_Uri::encode($delivery->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', __('Edit delivery'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_delivery.tpl');
	}
	
	public function editDelivery2(){
		$clazz = $this->getCurrentClass();
		$delivery = $this->getCurrentDelivery();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $delivery);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$delivery = $this->service->bindProperties($delivery, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($delivery->uriResource));
				$this->setData('message', __('Delivery saved'));
				$this->setData('reload', true);exit;
			}
		}
			
		
		// $handle = fopen("/formRender.html","wb");
		// $content = fwrite($handle,$myForm->render());
		// fclose($handle);
		$this->setData('secret', "somethign");
		$this->setData('formPlus', $myForm->render());
		$this->setView('cool.tpl');
	}
	
	//function test of 
	public function formPlus(){
		$clazz = $this->getCurrentClass();
		$delivery = $this->getCurrentDelivery();
		$formName="formPlus";
		
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $delivery, $formName, array("noSubmit"=>true,"noRevert"=>true));
		// $myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $delivery, $formName);
		
		$element = tao_helpers_form_FormFactory::getElement("buttonId", "Button");
		$myForm->addElement($element);
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$delivery = $this->service->bindProperties($delivery, $myForm->getValues());
				echo "ok!";exit;
			}
		}
		
		$this->setData('section', $formName);
		$this->setData('formPlus', $myForm->render());
		$this->setView('tree_form.tpl');
	}
	
	
	
	/**
	 * Add a delivery instance        
	 * @return void
	 */
	public function addDelivery(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$delivery = $this->service->createInstance($clazz);
		if(!is_null($delivery) && $delivery instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $delivery->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($delivery->uriResource)
			));
		}
	}
	
	/**
	 * Add a delivery subclass
	 * @return void
	 */
	public function addDeliveryClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createDeliveryClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Delete a delivery or a delivery class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteDelivery($this->getCurrentDelivery());
		}
		else{
			$deleted = $this->service->deleteDeliveryClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Duplicate a devliery instance
	 * @return void
	 */
	public function cloneDelivery(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$delivery = $this->getCurrentDelivery();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($delivery->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($delivery->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * display the authoring  template 
	 * @return void
	 */
	public function authoring(){
		$this->setData('error', false);
		try{
			// $deliveryInstance = $this->getCurrentDelivery();
			// $processInstance = $deliveryInstance->getUniquePropertyValue(new core_kernel_classes_Resource(TAO_DELIVERY_DELIVERYCONTENT));
			
			// $this->setData('delivery', tao_helpers_Uri::encode($deliveryInstance->uriResource));
			// $this->setData('process', tao_helpers_Uri::encode($processInstance->uriResource));
			
			//get process instance to be authored
			$processInstance = new core_kernel_classes_Resource("http://127.0.0.1/middleware/demo.rdf#i1265636054002217400");		
			$this->setData('processUri', tao_helpers_Uri::encode($processInstance->uriResource));
		}
		catch(Exception $e){
			$this->setData('error', true);
			$this->setData('errorMessage', $e);
		}
		$this->setView('process_authoring.tpl');
	}
	
	/**
	 * Get the data to populate the tree of delivery's subjects
	 * @return void
	 */
	public function getSubjects(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_SUBJECT_CLASS), true, true, ''));
	}
	
	/**
	 * Save the delivery excluded subjects
	 * @return void
	 */
	public function saveSubjects(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$subjects = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($subjects, tao_helpers_Uri::decode($value));
			}
		}
		
		if($this->service->setExcludedSubjects($this->getCurrentDelivery(), $subjects)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Get the data to populate the tree of delivery campaigns
	 * @return void
	 */
	public function getCampaigns(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS), true, true, ''));
	}
	
	/**
	 * Save the delivery related campaigns
	 * @return void
	 */
	public function saveCampaigns(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$campaigns = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($campaigns, tao_helpers_Uri::decode($value));
			}
		}
		
		if($this->service->setRelatedCampaigns($this->getCurrentDelivery(), $campaigns)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Main action
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function index(){
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		$this->setData('section',Session::getAttribute('currentSection'));
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the list of available delivery 
	 * It provides the value of the delivery properties such as label, uri and active and compiled status
	 *	 
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function deliveryListing(){
		$allTestArray=$this->service->getTestClass()->getInstances(true);
		$testData=array();
		$i=0;
		foreach($allTestArray as $test){
		
			$testData[$i]=array();
			$testData[$i]["label"]=$test->getLabel();
			$testData[$i]["uri"]=$test->uriResource;
			$testData[$i]["id"]=tao_helpers_Precompilator::getUniqueId($test->uriResource);
			$testData[$i]["compiled"]=0;
			$testData[$i]["active"]=0;
			
			$isCompiled=$this->service->getTestStatus($test, "compiled");
			if($isCompiled){
				$testData[$i]["compiled"]=1;
				$testData[$i]["active"]=1;
			}else{
				//if not, check if it is active:
				$isActive=$this->service->getTestStatus($test, "active");
				if($isActive){
					$testData[$i]["active"]=1;
				}
			}
			$i++;
		}
		$result=array();
		$result["tests"]=$testData;
		echo json_encode($result);
	}
	
	public function compileView(){
		$currentDelivery = $this->getCurrentDelivery();
		if(!is_null($currentDelivery)){
		
			$this->setData("deliveryUri", urlencode($currentDelivery->uriResource));
			$this->setData("deliveryLabel", $currentDelivery->getLabel());
		
			//compilation state:
			$isCompiled=$this->service->isCompiled($currentDelivery);
			$this->setData("isCompiled", $isCompiled);
			if($isCompiled){
				$compiledDate = $currentDelivery->getLastModificationDate(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP));
				$this->setData("compiledDate", $compiledDate->format('d/m/Y H:i:s'));
			}
		}
		
		$this->setView("compiling.tpl");
	}
	
	public function compiletest(){
		$deliveryData=array();
		
		// $currentDelivery = $this->getCurrentDelivery();
		// if(is_null($currentDelivery)){
			//no need to throw en exception here because it has already be done in the getCurrentDelivery() function
			// throw new Exception("no current delivery identified");
			
		// }else{
			// $deliveryUri = $currentDelivery->uriResource;
		// }
		
		$deliveryUri=$_SESSION["aaa"];
		echo json_encode(array('uri'=>$deliveryUri));
	}
	
	public function initCompilation(){
		//config:
		$pluginPath=BASE_PATH."/models/ext/deliveryRuntime/";
		$compilationPath=BASE_PATH."/compiled/";
		
		//get the uri of the test
		$deliveryUri = urldecode($_POST["uri"]);
		$delivery = new core_kernel_classes_Resource($deliveryUri);
		// $deliveryId=tao_helpers_Precompilator::getUniqueId($deliveryUri);
		
		//initiate compilator:
		$compilator = new tao_helpers_Precompilator($deliveryUri, $compilationPath, $pluginPath);//new constructor
		//delete the compiled delivery folder if it exists
		$compilator->clearCompiledFolder();

		//init the value to be returned	
		$deliveryData=array();
		
		//compilation state:
		$deliveryData["compiled"]=0;
		if($this->service->isCompiled($delivery)){
			$deliveryData["compiled"]=1;
			$deliveryData["compiledDate"] = $delivery->getLastModificationDate(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP))->format('d/m/Y H:i:s');
		}
		
		//get the tests list from the delivery id: likely, by parsing the deliveryContent property value
		//array of resource, test set
		$tests=array();
		$tests=array(
			new core_kernel_classes_Resource('http://127.0.0.1/middleware/demo.rdf#i1263198947080530400'),
			new core_kernel_classes_Resource('http://127.0.0.1/middleware/demo.rdf#i1263807554074753100'),
			new core_kernel_classes_Resource('http://127.0.0.1/middleware/demo.rdf#i1263807558034177900')
			);
			
		$deliveryData['tests']=array();
		foreach($tests as $test){
			$deliveryData['tests'][]=array(
				"label" => $test->getLabel(),
				"uri" => $test->uriResource
			);
		}
		
		$deliveryData["uri"]=$deliveryUri;
		
		echo json_encode($deliveryData);
	}
	
	/**
	 * Compile a test by providing its uri, via POST method.
	 * Its main purpose is to collect every required resource to run the test in a single folder so they become immediately available for the test launch, without any delay. 
	 * The resources are test and item runtime plugins and media files.
	 * This action parses the testContent and itemContent and save a copy of these files in the compiled test directory.
	 * The action compiles every available language for a given test at once.
	 * It provides a json string to indicate the success or failure of the test compilation
	 * The recompilation of an already compiled test will erase the previously created compiled files.
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function compile(){
		//should be renamed  "compileTest()" now
		
		//config:
		$pluginPath=BASE_PATH."/models/ext/deliveryRuntime/";
		$compilationPath=BASE_PATH."/compiled/";
		
		//get the unique id of the test to be compiled from POST
		$testUri=$_POST["uri"];
		$testId=tao_helpers_Precompilator::getUniqueId($testUri);
		
		//copy runtime plugins:
		$compilator = new tao_helpers_Precompilator($testUri, $compilationPath, $pluginPath);//new constructor
		$compilator->copyPlugins();
		
		//directory where all files required to launch the test will be collected
		$directory=$compilator->getCompiledPath();
		
		//get the test object from the testUri
		$aTestInstance = new core_kernel_classes_Resource($testUri);
		
		//set the compiled status to "false" in case any unforseen problem should occur
		$aTestInstance->editPropertyValues(new core_kernel_classes_Property(TEST_COMPILED_PROP),GENERIS_FALSE);
		
		//check whether the test is active or not:
		$testActive = $this->service->getTestStatus($aTestInstance, "active");
		if(!$testActive){
			throw new Exception("The test '$testUri' is not active so cannot be compiled.");
		}
		
		//get the language code of available languages for the current test:
		$testContentProperty = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
		$languages = array();
		$languages = $aTestInstance->getUsedLanguages($testContentProperty);
		
		$testContentArray = array();//array of XML files containing the testContent in every available langauge
		
		foreach($languages as $language){
			
			$testContentCollection = $aTestInstance->getPropertyValuesByLg($testContentProperty, $language);
			if($testContentCollection->count() == 1){
				//string version of the testContent aimed at being modified
				$testContentArray[$language] = $testContentCollection->get(0)->literal;
			}
			else{
				throw new Exception("The test collection for the language '$language' must not be empty");
			}
			
			//dom version of testContent for easy parsing purpose
			$testContentDom = new DomDocument();//testContent in the given language, converted into an XML file with:  $dom = new DomDocument(); $dom->loadXML($chaineXML);
			@$testContentDom->loadHTML($testContentArray[$language]);
			
			//fetch the uri of all Items of the Test instance in the given language, by  parsing the testContent DOM
			$items=$testContentDom->getElementsByTagName('citem');
			
			//add the last item to upload the test result
			$sequence=$items->length+1;
			$testContentArray[$language]=str_replace('</tao:TEST>','<tao:CITEM weight="0" Sequence="'.$sequence.'" itemModel="taotab.swf">uploadItem</tao:CITEM></tao:TEST>',$testContentArray[$language]);
			
			foreach ($items as $item){
				$itemUri=$item->nodeValue;
				//get an unique item id from its uri
				$itemId=tao_helpers_Precompilator::getUniqueId($itemUri);
				
				$anItemInstance = new core_kernel_classes_Resource($itemUri);
				
				/*
				 * @require taoItems extension 
				 * @see taoItems_models_classes_ItemsService::getAuthoringFile
				 */
				$itemModel = null;
				$itemContent = null;

				//get the black file into file system instead of the RDF triple for the HAWAI item models
				try{
					$itemModel = $anItemInstance->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
					if($itemModel instanceof core_kernel_classes_Resource){
						if($itemModel->uriResource == TAO_ITEM_MODEL_WATERPHENIX){
							
							//get content into black file
							$itemService = tao_models_classes_ServiceFactory::get('Items');
							
							//Common black file not used now
							//$itemContent = file_get_contents($itemService->getAuthoringFile($anItemInstance->uriResource));
							
							//Temp Common black file used instead
							//@todo use the common and clean black file once the runtime implement it
							$itemContent = file_get_contents($itemService->getTempAuthoringFile($anItemInstance->uriResource));
						}
					}
				}
				catch(Exception $e){}
				if(is_null($itemContent)){
					$itemContentCollection = $anItemInstance->getPropertyValuesByLg(new core_kernel_classes_Property(ITEM_ITEMCONTENT_PROP), $language);
				
					//get ItemContent in the given language, which is an XML file, in the language defined by $language
					if($itemContentCollection->count() == 1){//there should be only one per language
						$itemContent=$itemContentCollection->get(0)->literal;//string version of the itemContent aimed at being parsed and modified
					}
					else{
						throw new Exception("Incorrect number of elements in item collection: ".$itemContentCollection->count() );
					}
				}
				
				
				//parse the XML file with the helper Precompilator: media files are downloaded and a new xml file is generated, by replacing the new path for these media with the old ones
				$itemContent=$compilator->itemParser($itemContent,$directory,"$itemId$language.xml");//rename to parserItem()
				
				//create and write the new xml file in the folder of the test of the delivery being compiled (need for this so to enable LOCAL COMPILED access to the media)
				$compilator->stringToFile($itemContent, $directory, "$itemId$language.xml");
				
				//add another parser to define the new path to the item's xml file in the Test.Language.xml file. 
				$escapedItemUri=preg_replace('/\//', "\/", $itemUri);
				$testContentArray[$language]=preg_replace("/$escapedItemUri/", $itemId.$language, $testContentArray[$language], 1);
			}
			//when the compilation in a language is done, write the new test xml file associated to the language:
			$compilator->stringToFile($testContentArray[$language], $directory, "test$language.xml");//nom de la var $testContentArray[$language]
			
		}//end of foreach language of test
		
		//create a test.xml file with links to the file of all test languages
		$testXMLfile="";
		$testXMLfile='<?xml version="1.0" encoding="UTF-8"?>
		<tao:TEST rdfid="'.$testUri.'" xmlns:tao="http://www.tao.lu/tao.rdfs#" xmlns:rdfs="http://www.w3.org/TR/1999/PR-rdf-schema-19990303#">';
		foreach ($languages as $language){
			$testXMLfile.="<tao:TESTcontent lang=\"$language\">test$language</tao:TESTcontent>";
		}
		$testXMLfile.='</tao:TEST>';
		
		$compilator->stringToFile($testXMLfile, $directory, "Test.xml");
		
		//then send the success message to the user
		$resultArray=array();
		$compilationResult=$compilator->result();
		
		if(empty($compilationResult["failed"]["copiedFiles"]) && empty($compilationResult["failed"]["createdFiles"]) ){
			//compilation success
			$resultArray["success"]=1;
			
			//if everything works well, set the property of the delivery(for now, one single test only) "compiled" to "True" 
			// $aTestInstance->setPropertyValue(new core_kernel_classes_Property(TEST_COMPILED_PROP),GENERIS_TRUE);
			$aTestInstance->editPropertyValues(new core_kernel_classes_Property(TEST_COMPILED_PROP),GENERIS_TRUE);
			
		}elseif(!empty($compilationResult["failed"]["copiedFiles"]) and empty($compilationResult["failed"]["createdFiles"]) and empty($compilationResult["failed"]["copiedFiles"]["delivery_runtime"])){
			//success with warning: media missing: some file copying failed but, every required runtime plugin is successfully copied.
			$resultArray["success"]=2;
			$resultArray["failed"]=$compilationResult["failed"];
			
			//unquote the following line if the compilation can be considered completed
			$aTestInstance->editPropertyValues(new core_kernel_classes_Property(TEST_COMPILED_PROP),GENERIS_TRUE);
			
		}else{
			//other cases: the compilation has failed
			$resultArray["success"]=0;
			$resultArray["failed"]=$compilationResult["failed"];
			
		}
		
		echo json_encode($resultArray);
	}
	
	public function endCompilation(){
	
		$uri = urldecode($_POST["uri"]);
		$delivery = new core_kernel_classes_Resource($uri);
		
		$response = array();
		$response["result"]=0;
		
		if ($delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP),GENERIS_TRUE)){
			$response["result"] = 1;
			$response["compiledDate"] = $delivery->getLastModificationDate(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP))->format('d/m/Y H:i:s');
			
		}
		
		echo json_encode($response);
	}
	
	/**
	 * From the uri of a compiled test, this action will redirect the user to the compiled test folder
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function preview($testUri=''){
		//get encoded url
		$testUri=urldecode($_GET["uri"]);
		
		//firstly check if the delivery instance is compiled or not
		$aTestInstance = new core_kernel_classes_Resource($testUri);
		try{
			$testCompiled=$this->service->getTestStatus($aTestInstance, "compiled");
		}
		catch(Exception $e){ echo $e;}
		
		if($testCompiled){
			$testId=tao_helpers_Precompilator::getUniqueId($testUri);
			$testUrl=BASE_URL."/compiled/$testId/theTest.php?subject=previewer";
			header("location: $testUrl");
		}else{
			echo "Sorry, the test seems not to be compiled.<br/> Please compile it then try again.";
		}
	}
	
	/**
	 * create history table
	 * @return void
	 */
	public function viewHistory(){
		
		$_SESSION['instances'] = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^uri_[0-9]+$/", $key)){
				$_SESSION['instances'][tao_helpers_Uri::decode($value)] = tao_helpers_Uri::decode($value);
			}
		}
		$this->setView("create_table.tpl");
	}
	
	/**
     * historyListing returns the execution history related to a given delivery (and subject)
     * 
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string deliveryUri
	 * @param  string subjectUri
     * @return array
     */
	public function historyListing($deliveryUri = "", $subjectUri = ""){
		
		$returnValue = array();
		$historyCollection = null;
		
		//check deliveryUri validity
		if(empty($deliveryUri)){
			$currentDelivery = $this->getCurrentDelivery();
			if(is_null($currentDelivery)){
				//no need to throw en exception here because it has already be done in the getCurrentDelivery() function
				return $returnValue;
			}else{
				$deliveryUri = $currentDelivery->resourceUri;
			}
		}
		
		$historyCollection=$this->service->getHistory($deliveryUri, $subjectUri);
		
		$i=0;
		foreach ($historyCollection->getIterator() as $history) {
		
			$returnValue[$i]=array();
			
			if(!empty($subjectUri)){
				$subject=$history->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP));
				$returnValue[$i]["subject"] = $subject->getLabel(); //or $subject->literal to get the uri
			}
			$timestamp=$history->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP));
			$returnValue[$i]["timestamp"] = date('d-m-Y', $timestamp->literal);
			
			// $delivery=$history->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_DELIVERY_PROP));//useless
			
			$i++;
		}
			
		return $returnValue;
		//TODO: history listing per subject???
	}
	
	public function cacheView(){
		$this->setView("cache.tpl");
	}
	
	/*
	public function cache(){
		//get the id of subscribed modules and connect to them
		core_kernel_classes_ApiModelOO::getSubscription() : array(ids) NOT defined yet?
		core_kernel_classes_ApiModelOO::connectOnRemoteModule($idSubscription) : boolean NIY
		
		//get rdf file of these modules AND parse the rdf files (input: rdf dom object? or file location?)
		core_kernel_classes_ApiModelOO::exportXmlRdf() : String NIY

		//generate a new and unique namespace (thus new modelId) for the (each?) modules
		//store the triplets with this namespace:
		core_kernel_classes_ApiModelOO::importXmlRdf( java_lang_String $targetNameSpace,  java_lang_String $fileLocation) : Boolean NIY

		//save the reference to these new local namespaces ("s" for several modules) in the cache properties (one value for each cached module) of the 
		//q? given a delivery and a module, allow multiple cache? should be no, since delivery is aimed at being used within a limited time period
		
		//return the success/failure status of each distant module caching
	}
	*/
	
	/*
	 * @TODO implement the following actions
	 */
	
	public function getMetaData(){
		throw new Exception("Not implemented yet");
	}
	
	public function saveComment(){
		throw new Exception("Not implemented yet");
	}
	
}
?>