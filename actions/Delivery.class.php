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
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		$testId="";//get the unique id of the test, by extracting the id from the uri of the test reference $uri
		$directory="taoDelivery/compiledTests/$testId/";//directory where all files related to this test(i.e media files and item xml files)
		//create a directory:
		mkdir($directory);
		
		//get language Code of the available languages for the test:
		//use getUsedLanguages( java_lang_String $uriProperty) when it is implemented
		$languages=array();
		$languages[]="EN";//for test
		
		$testXML=array();//array of XML file
		
		foreach($languages as $language){
			$testContent="";//string XML got from an API call
			$testXML[$language]=new DomDocument();//testContent in the given language, converted into an XML file with:  $dom = new DomDocument(); $dom->loadXML($chaineXML);
			@$testXML[$language]->loadHTML($testContent);
			
			//fetch the uri of all Items of the Test instance in the given language:
			$items=array();//getPropertyValuesCollection(Property relatedItem)
			$items=$testXML[$language]->getElementsByTagName('citem');
			$items=array('http://127.0.0.1/middleware/demoItems.rdf#113567805632546');//for test
			
			$testXML[$language]=$testContent;//reload with the string value
			
			foreach ($items as $item){
				$itemUri=$item->nodeValue;
				//get item id from its uri (e.g. http://127.0.0.1/middleware/demoItems.rdf#113567805632546)
				$itemId=substr($itemUri,stripos($itemUri,".rdf#")+5);//TODO check format of the uri
				
				//get ItemContent in the given language, which is an XML file
				//getPropertyValuesCollection(Property relatedItem)
				$itemContent="";//xml file in the given language
				
				//parse the XML file with the helper Precompilator: media files are downloaded and a new xml file is generated, by replacing the new path for these media with the old ones
				$compilator = new tao_helpers_Precompilator();
				$itemContent=$compilator->parser($itemContent,$directory);
				
				//add another parser to define in the Test.Language.xml file, the new path to the item's xml file. 
				preg_replace($itemUri, $itemId.$language, $testXML[$language], 1);
				
				//create and write the new xml file in the folder of the test of the delivery being compiled (need for this so to enable LOCAL COMPILED access to the media)
				$itemXMLpath = "$directory/$itemId$language.xml";
				$handle = fopen($xmlItemPath,"wb");
				$xmlItem = fwrite($handle,$xmlItem);
				fclose($handle);
				
				//TODO: handle the case when item éissing or other issues
			}
			//when a language is done, write the new test xml file associated to the language:
			$handle = fopen("$directory/test$language.xml","wb");
			$testXML[$language] = fwrite($handle,$testXML[$language]);
			fclose($handle);
		}
		
		//create a new test.xml file with link to all test languages
		$testFile="";
		
		$handle = fopen("$directory/test.xml","wb");
		$testFile = fwrite($handle,$testFile);
		fclose($handle);
		
		//if everything works well, set the property of the delivery(for now, one single test only) "compiled" to "True" 
		
		//then send the success message to the user
	}
}
?>