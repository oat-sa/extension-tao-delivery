<?php
require_once('tao/actions/CommonModule.class.php');
require_once('taoDelivery/helpers/class.Precompilator.php');

/**
 * Delivery Controller provide actions performed from url resolution
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class Delivery extends CommonModule {
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Groups
	 */
	public function __construct(){
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Delivery');
		$this->defaultData();
	}
	
/*
 * controller actions
 */

	/**
	 * Main action
	 * @return void
	 */
	public function index(){
		// self::compile();
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the list of available delivery 
	 * It provides the value of the delivery properties such as label, uri and active and compiled status 
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
		
	/**
	 * Compile a test by providing its uri, via POST method.
	 * Its main purpose is to collect every required resources in a single folder so they become immediately available for test launch, without delay. 
	 * The resources are test and item runtime plugins and media files.
	 * It parses the testContent and itemContent and save a copy of these files in the compiled test directory.
	 * The action compiles every available language for a given test at once.
	 * It provides a json string to indicate the success or failure of the test compilation
	 * The recompilation of an already compiled test will erase the previously created compiled files.
	 * @return void
	 */
	public function compile(){
		//config:
		$pluginPath=BASE_PATH."/models/ext/deliveryRuntime/";
		$compilationPath=BASE_PATH."/compiled/";
		
		//get the unique id of the test to be compiled from POST
		$testUri=$_POST["uri"];
		$testId=tao_helpers_Precompilator::getUniqueId($testUri);
		
		//copy runtime plugins:
		$compilator = new tao_helpers_Precompilator($testUri, $compilationPath, $pluginPath);//new constructor
		//$compilator = new tao_helpers_Precompilator($directory, $pluginPath);//old constructor that does manage directory creation errors properly
		$compilator->copyPlugins();
		
		//directory where all files required to launch the test yill be collected
		$directory=$compilator->compiledPath;
		
		$aTestInstance = new core_kernel_classes_Resource($testUri);
		
		//check whether the test is active or not:
		$testActive=$this->service->getTestStatus($aTestInstance, "active");
		if(!$testActive){
			throw new Exception("The test '$testUri' is not active so cannot be compiled.");
		}
		
		//get the language code of available languages for the current test:
		$testContentProperty = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
		$languages=array();
		$languages = $aTestInstance->getUsedLanguages($testContentProperty);
		
		$testContentArray=array();//array of XML files containing the testContent in every available langauge
		
		foreach($languages as $language){
			
			$testContentCollection = $aTestInstance->getPropertyValuesByLg($testContentProperty, $language);
			if($testContentCollection->count() == 1){
				//string version of the testContent aimed at being modified
				$testContentArray[$language]=$testContentCollection->get(0)->literal;
			}
			else{
				throw new Exception("The test collection for the language '$language' must not be empty");
			}
			// print_r($testContentArray);
			
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
				$itemContentCollection = $anItemInstance->getPropertyValuesByLg(new core_kernel_classes_Property(ITEM_ITEMCONTENT_PROP), $language);
				
				//get ItemContent in the given language, which is an XML file, in the language defined by $language
				if($itemContentCollection->count() == 1){//there should be only one per language
					$itemContent=$itemContentCollection->get(0)->literal;//string version of the itemContent aimed at being parsed and modified
				}
				else{
					throw new Exception("Incorrect number of elements in item collection: ".$itemContentCollection->count() );
				}
				//debug
				// $compilator->stringToFile($itemContent, $directory, "preparsed_$itemId$language.xml");
				
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
			$aTestInstance->setPropertyValue(new core_kernel_classes_Property(TEST_COMPILED_PROP),GENERIS_TRUE);
			
		}elseif(!empty($compilationResult["failed"]["copiedFiles"]) and empty($compilationResult["failed"]["createdFiles"]) and empty($compilationResult["failed"]["copiedFiles"]["delivery_runtime"])){
			//success with warning: media missing: some file copying failed but, every required runtime plugin is successfully copied.
			$resultArray["success"]=2;
			$resultArray["failed"]=$compilationResult["failed"];
			
			//unquote the following line if the compilation can be considered completed
			$aTestInstance->setPropertyValue(new core_kernel_classes_Property(TEST_COMPILED_PROP),GENERIS_TRUE);
			
		}else{
			//other cases: the compilation has failed
			$resultArray["success"]=0;
			$resultArray["failed"]=$compilationResult["failed"];
		}
		
		echo json_encode($resultArray);
	}
	
	public function preview(){
		//get encoded url
		$testUri=urldecode($_GET["uri"]);
		
		//firstly check if the delivery instance is compiled or not
		$aTestInstance = new core_kernel_classes_Resource($testUri);
		try{
			$testCompiled=$this->service->getTestStatus($aTestInstance, "compiled");
		}
		catch(Exception $e){ echo $e;}
		
		// $testCompiled=true;
		// $testUri = 'http://127.0.0.1/middleware/demo.rdf#8888';
		if($testCompiled){
			$testId=tao_helpers_Precompilator::getUniqueId($testUri);
			$testUrl=BASE_URL."/compiled/$testId/theTest.php?subject=previewer";
			header("location: $testUrl");
		}else{
			echo "Sorry, but the test seems not to be compiled.<br/> Please compiled it first and try again.";
		}
	}
	
}
?>