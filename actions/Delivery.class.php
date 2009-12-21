<?php


require_once('tao/actions/CommonModule.class.php');
require_once('taoDelivery/helpers/class.Precompilator.php');

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class Delivery extends CommonModule {
	
	public function __construct(){
		
		//parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Delivery');
		$this->defaultData();
	}
	
	public function index1(){
		$allTests=array();
		//fetch Test Instances from test ontology
		$testClass = $this->service->getTestClass();
		$allTests=$this->service->getTestClass()->getInstances(true);
		print_r($allTests);
		$testListing="<ul>";
		foreach($allTests as $test){
			//get the values of the properties of each instance: label, some parameter, compiled or not
			$testLabel=$test->getLabel();
			$testUri=$test->uriResource;
			
			//check whether it is active or not (i.e. available for compilation)
			$testActive=$this->service->getTestStatus($test, "active");
			// if($testActive) $testActiveVal="Active";//for ttest only
			
			$testCompiled=false;
			foreach ($test->getPropertyValuesCollection(new core_kernel_classes_Property(TEST_COMPILED_PROP))->getIterator() as $value){
				if($value instanceof core_kernel_classes_Resource ){
					if ($value->uriResource == GENERIS_TRUE){
						$testCompiled=true;
						$testCompiledVal="Compiled";
					}
				}
			}
			
			//format the information to prepare it for the view
			$testListing.="<li>$testLabel: $testUri *$testActiveVal*$testCompiledVal*<a href=\"compile\">compile</a></li>";
			
			if($testCompiled){
				//add "preview button"
			}else{
				//add "compile button"
			}
		}
		$testListing.="</ul>";
		
		$content=$testListing;
		$content.='<a href="/taoDelivery/Delivery/preview?uri=123" >kljhkghhjg</a>';
		
		// self::compile();
		
		$this->setData('content', $content);
		$this->setView('index.tpl');
	}
	public function index2(){
		// $tests=$this->service->getTestsBySubject("http://127.0.0.1/middleware/demo.rdf#i1260883022085327900");
		// var_dump($tests);
		// var_dump($this->service->getTestStatus(new core_kernel_classes_Resource($tests[0]), "active"));
		echo $this->service->getTestStatus(new core_kernel_classes_Resource('http://127.0.0.1/middleware/demo.rdf#9999'), "compiled");
		// echo GENERIS_TRUE;
	}
	
	public function index(){
		// self::compile();
		$this->setView('index.tpl');
	}
	
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
			
			//check whether it is compiled or not, and select only the compiled one
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
		// var_dump($testData);
		$result=array();
		$result["tests"]=$testData;
		echo json_encode($result);
	}	
		
	//TODO progress bar plus interruption or exception management
	public function compile(){
		//config:
		$pluginPath="./models/ext/deliveryRuntime/";
		$compilationPath="./compiled/";
		
		//get the uri of the test to be compiled
		// $testUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		// $testUri = 'http://127.0.0.1/middleware/demo.rdf#125187505335708';//myCTest
		// $testUri = 'http://127.0.0.1/middleware/demo.rdf#9999';
		// $testId=tao_helpers_Precompilator::getUniqueId($testUri);//get the unique id of the test, by extracting the id from the uri of the test reference $testUri
		
		//get the unique id from POST
		$testUri=$_POST["uri"];
		$testId=tao_helpers_Precompilator::getUniqueId($testUri);
		
		//create a directory where all files related to this test(i.e media files and item xml files) will be copied
		// $directory="./compiled/$testId/";
		// if(!is_dir($directory)){
			// mkdir($directory);
		// }
		
		//copy plugin here:
		$compilator = new tao_helpers_Precompilator($testUri, $compilationPath, $pluginPath);//new constructor
		// $compilator = new tao_helpers_Precompilator($directory, $pluginPath);//old constructor that does manage directory creation errors properly
		$compilator->copyPlugins();
		
		$directory=$compilator->compiledPath;
		
		$aTestInstance = new core_kernel_classes_Resource($testUri);
		
		//check whether the test is active or not:
		$testActive=$this->service->getTestStatus($aTestInstance, "active");
		$testCompiled=$this->service->getTestStatus($aTestInstance, "compiled");
		
		//get the language code of available languages for the current test
		$testContentProperty = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
		$languages=array();
		$languages = $aTestInstance->getUsedLanguages($testContentProperty);
		
		$testContentArray=array();//array of XML file containing the testContent in every available langauge
		
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
			$testContentArray[$language]=str_replace('</tao:TEST>','<tao:CITEM weight="0" Sequence="'.$sequence.'">uploadItem</tao:CITEM></tao:TEST>',$testContentArray[$language]);
			
			foreach ($items as $item){
				$itemUri=$item->nodeValue;
				//get an unique item id from its uri
				$itemId=tao_helpers_Precompilator::getUniqueId($itemUri);
				
				$anItemInstance = new core_kernel_classes_Resource($itemUri);
				$itemContentCollection = $anItemInstance->getPropertyValuesByLg(new core_kernel_classes_Property(ITEM_ITEMCONTENT_PROP), $language);
				
				//get ItemContent in the given language, which is an XML file, in the language defined by $language
				if($itemContentCollection->count() == 1){//there should be only one per language
					//string version of the itemContent aimed at being parsed and modified
					$itemContent=$itemContentCollection->get(0)->literal;
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
				
				//copy required the runtime component in the created test directory
				//assumption 1: direct access to required plugins with the parameter Item_model_runtime
				//assumption 2: no need to rename the plugin file path in the item.xml or test.xml file
				//assumption 3: one unique runtime for an item model
				/*
				if($itemModel instanceof core_kernel_classes_Resource){
					$runtime = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(ITEM_MODEL_RUNTIME_PROP));
					if($runtime instanceof core_kernel_classes_Literal ){
						if(preg_match("/\.swf$/", (string)$runtime)){
							$compilator->copyFile($pluginPath.$runtime,$directory,"$itemId$language.xml");
						}
					}
				}*/
				//TODO: handle the case when item missing or other issues
			}
			//when the compilation in a language is done, write the new test xml file associated to the language:
			$compilator->stringToFile($testContentArray[$language], $directory, "test$language.xml");//nom de la var $testContentArray[$language]
			
		}//end of foreach language of test
		//create a test.xml file with links to all test languages
		$testXMLfile="";
		$testXMLfile='<?xml version="1.0" encoding="UTF-8"?>
		<tao:TEST rdfid="'.$testUri.'" xmlns:tao="http://www.tao.lu/tao.rdfs#" xmlns:rdfs="http://www.w3.org/TR/1999/PR-rdf-schema-19990303#">';
		foreach ($languages as $language){
			$testXMLfile.="<tao:TESTcontent lang=\"$language\">test$language</tao:TESTcontent>";
		}
		$testXMLfile.='</tao:TEST>';
		
		$compilator->stringToFile($testXMLfile, $directory, "test.xml");
		
		//copy the start.php file to the compiled test folder, where the flash plugins will be embedded
		/*
		$testPlugins=array("test.swf","CLLPlugin.swf","start.php");
		foreach($testPlugins as $testPlugin){
			$compilator->copyFile($pluginPath.$testPlugin, $directory, 'testFolder');
		}*/
		
		//then send the success message to the user
		$resultArray=array();
		$compilationResult=$compilator->result();
		
		// print_r($compilationResult);//debug
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
			//other cases: the compilation fails
			$resultArray["success"]=0;
			$resultArray["failed"]=$compilationResult["failed"];
		}
		
		echo json_encode($resultArray);
	}
	
	/*
	public function preview(){
		$testUri=$_GET["uri"];
		
		//firstly check if the delivery instance is compiled or not
		$aTestInstance = new core_kernel_classes_Resource($testUri);
		try{
			$testCompiled=$this->service->getTestStatus($aTestInstance, "compiled");
		}
		catch(Exception $e){ echo $e;}
		
		$testCompiled=true;
		$testUri = 'http://127.0.0.1/middleware/demo.rdf#8888';
		if($testCompiled){
			$testId=tao_helpers_Precompilator::getUniqueId($testUri);
			$testUrl="../compiled/$testId/start.php?testSubject=preview";
			header("location: $testUrl");
		}
	}
	*/
}
?>