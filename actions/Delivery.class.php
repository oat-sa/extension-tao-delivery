<?php
require_once('tao/actions/CommonModule.class.php');
require_once('taoDelivery/helpers/class.Precompilator.php');

class Delivery extends CommonModule {
	
	public function __construct(){
		
		//parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Delivery');
		$this->defaultData();
	}
	
	public function index(){
		echo GENERIS_TRUE;
	}

	public function index0(){
	/**
	*Tests preliminaires
	*/
	/*
		$highlightUri = '';
		//$content = json_encode( $this->service->toTree( $this->service->getDeliveryClass(), true, true, $highlightUri));
		// $content = '';
		// var_dump($this->service->getDeliveryClass());
		
		//test pour creer un delivery:
		// var_dump($this->service->createDelivery('Test Delivery' ,  'It is the nieth test sequence'));
		
		//test pour creer afficher toutes les instances de la classe, avec les propietes:
		//$allInstances=$this->service->getAllDeliveries($this->service->getDeliveryClass());
		$allInstances=tao_models_classes_Service::toArray($this->service->getDeliveryClass());
		var_dump(json_encode( ($allInstances) ));
	*/	
	
	/*
		//tests de creations:
		
		//creer une sous classe de Delivery avec les proprietes maxexec, start, end
		// $properties = array("maxexec"=>"N/A",
							// "start"=>"N/A",
							// "end"=>"N/A");
		// $this->service->createDeliveryClass(null, $label = 'Another class of Delivery',$properties);
		
		//uri de cette nouvelle classe de Delivery: http://127.0.0.1/middleware/demoDelivery.rdf#i1259765004051938800		
		$clazz = new core_kernel_classes_Class('http://127.0.0.1/middleware/demoDelivery.rdf#i1259765004051938800');
		// $clazz = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);//pour creer une instance a la classe de delivery a la racine
		
		//creer une sous sous classe de Delivery avec les prop subjects, groups et tests en plus
		$properties = array("subjects"=>"N/A",
							"groups"=>"N/A",
							"tests"=>"N/A");
		$newClazz = $this->service->createDeliveryClass($clazz, $label = 'Another sub-class of Delivery',$properties);
		
		//creer une instance de cette classe et associer les valeurs aux propietes, a partir de leurs uri
		$anInstance = $this->service->createInstance($newClazz,"Brand new delivery!!!");
		
		$propertyValues = array();
		$uri_maxexec = "http://127.0.0.1/middleware/demoDelivery.rdf#i1259765004053436900";
		$uri_start =  "http://127.0.0.1/middleware/demoDelivery.rdf#i1259765004055337900";
		$uri_end = "http://127.0.0.1/middleware/demoDelivery.rdf#i1259765004057158400";
		$propertyValues = array( $uri_maxexec => '5',
								$uri_start => '2013',
								$uri_end => '2014');
		$group = $this->service->bindProperties($anInstance, $propertyValues);
		
	*/	
		//afficher toutes les instances de delivery

		
		$allInstances=tao_models_classes_Service::toArray($this->service->getDeliveryClass('http://127.0.0.1/middleware/demoDelivery.rdf#i1259765004051938800'));
		var_dump(json_encode( ($allInstances) ));
		
		// $uri_subjects="http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject";
		// $subjectsInfo=$this->service->subjectClass->getSubjectClass();
		// $subjects=tao_models_classes_Service::toArray($subjectsInfo);
		// var_dump(json_encode( ($this->service->subjectClass) ));
		// var_dump(json_encode( ($this->service) ));
		// $this->setData('content', $content);
		// $this->setView('index.tpl');
		
		$this->service->getSubjectInstances();
	
	/**
	*Start of the real implementation
	*/	
		$allTests=array();
		//fetch Test Instances from test ontology
		
		foreach($allTests as $test){
			//get the values of the properties of each instance: label, some parameter, compiled or not
			
			//format the information to prepare it for the view
			
			//add "preview button"
			
		}
		
	}
	
	//asynchronus action
	//TODO progress bar plus interruption or exception management
	public function compile(){
		//get the uri of the test to be compiled
		$testUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		$testId="";//get the unique id of the test, by extracting the id from the uri of the test reference $testUri
		
		//create a directory where all files related to this test(i.e media files and item xml files) will be copied
		$directory="./taoDelivery/compiled/$testId/";
		$directoryCreated = mkdir($directory);//TODO exception management
		
		//get the language Code of the available languages for the test:
		//use getUsedLanguages( java_lang_String $uriProperty) when it is implemented
		$languages=array();
		$languages=array('EN','FR');//for test
		
		$testContentArray=array();//array of XML file containing the testContent in every available langauge
		$testContentArray['EN']='';//for test
		$testContentArray['FR']='';
		
		$compilator = new tao_helpers_Precompilator();
		
		foreach($languages as $language){
			$testContent="";//string XML got from an API call (depend on the implementation of the API that is being done)
			
			//string version of the testContent aimed at being modified
			$testContentArray[$language]=$testContent;
			
			//dom version of testContent for easy parsing purpose
			$testContentDom = new DomDocument();//testContent in the given language, converted into an XML file with:  $dom = new DomDocument(); $dom->loadXML($chaineXML);
			@$testContentDom->loadHTML($testContent);
			
			//fetch the uri of all Items of the Test instance in the given language:
			$items=array();//getPropertyValuesCollection(Property relatedItem)
			$items=$testContentDom->getElementsByTagName('citem');
			$items=array('http://127.0.0.1/middleware/demoItems.rdf#113567805632546');//for test
			
			foreach ($items as $item){
				$itemUri=$item->nodeValue;
				//get item id from its uri (e.g. http://127.0.0.1/middleware/demoItems.rdf#113567805632546)
				$itemId=substr($itemUri,stripos($itemUri,".rdf#")+5);//TODO check format of the uri
				
				//get ItemContent in the given language, which is an XML file
				//getPropertyValuesCollection(Property relatedItem)
				$itemContent="";//xml file in the given language defined in $language
				
				//parse the XML file with the helper Precompilator: media files are downloaded and a new xml file is generated, by replacing the new path for these media with the old ones
				$itemContent=$compilator->itemParser($itemContent,$directory);//rename to parserItem()
				
				//create and write the new xml file in the folder of the test of the delivery being compiled (need for this so to enable LOCAL COMPILED access to the media)
				// $itemXMLpath = "$directory/$itemId$language.xml";//createXMLfile($directory,$xmlString)
				// $handle = fopen($xmlItemPath,"wb");
				// $itemContent = fwrite($handle,$itemContent);
				// fclose($handle);
				$compilator->stringToFile($itemContent, $directory, "$itemId$language.xml");
				
				//add another parser to define the new path to the item's xml file in the Test.Language.xml file. 
				$escapedItemUri=preg_replace('/\//', "\/", $itemUri);
				$testXML[$language]=preg_replace("/$escapedItemUri/", $itemId.$language, $testXML[$language], 1);
				
				//hypothesis: direct access to required plugins with the parameter Item_model_runtime (e.g. $runtime = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY));)
				//hypothesis 2: no need to rename the plugin file path in the item.xml or test.xml file
				//copy required component in the created test directory
				$pluginUri = "./taoDelivery/models/ext/itemRuntime/";
				$plugins=array();
				foreach ($plugins as $plugin){
					$compilator->downloadFile($pluginUri.$plugin,$directory,'');
				}
				
				//TODO: handle the case when item missing or other issues
			}
			//when the compilation in a language is done, write the new test xml file associated to the language:
			// $handle = fopen("$directory/test$language.xml","wb");
			// $testXML[$language] = fwrite($handle,$testXML[$language]);
			// fclose($handle);
			$compilator->stringToFile($testXML[$language], $directory, "test$language.xml");
			
			//if everything works well, set the property of the delivery(for now, one single test only) "compiled" to "True" 
			//the uri of the property "compiled" is 'http://www.tao.lu/Ontologies/TAOTest.rdf#i1260348091087274600'
			$propertyCompiled = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#i1260348091087274600'); 	
			$testInstance->setPropertyValue($propertyCompiled,GENERIS_TRUE);
			
			
		}//end of foreach language of test
		
		//create a new test.xml file with links to all test languages
		$testXMLfile="";
		$testXMLfile='<?xml version="1.0" encoding="UTF-8"?>
		<tao:TEST rdfid="'.$testUri.'" xmlns:tao="http://www.tao.lu/tao.rdfs#" xmlns:rdfs="http://www.w3.org/TR/1999/PR-rdf-schema-19990303#">';
		foreach ($languages as $language){
			$testXMLfile.="<tao:TESTcontent lang=\"$language\">$language</tao:TESTcontent>";
		}
		$testXMLfile.='</tao:TEST>';
		
		// $handle = fopen("$directory/test.xml","wb");
		// $testFile = fwrite($handle,$testXMLfile);
		// fclose($handle);
		$compilator->stringToFile($testXMLfile, $directory, "test.xml");
		
		//start.html file where the flash plugins will be embedded
		$compilator->downloadFile("../models/ext/itemRuntime/start.php",$directory,'');
		
		//then send the success message to the user
	}
}
?>