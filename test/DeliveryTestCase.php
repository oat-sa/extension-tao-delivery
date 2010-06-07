<?php
// require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
// set_include_path(get_include_path().';'.dirname(__FILE__).'/../..');

require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';
require_once dirname(__FILE__) . '/../includes/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

if(!defined("LOGIN")){
	define("LOGIN", "generis", true);
}
/**
* @constant password for the module you wish to connect to 
*/
if(!defined("PASS")){
	define("PASS", "g3n3r1s", true);
}
/**
* @constant module for the module you wish to connect to 
*/
if(!defined("MODULE")){
	define("MODULE", "tao", true);
}

error_reporting(E_ALL);

class DeliveryTestCase extends UnitTestCase {
	
	protected $deliveryService = null;
	protected $delivery = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		// TestRunner::initTest();
		// $this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		// $this->apiModel->logIn(LOGIN,md5(PASS),DATABASE_NAME,true);
		// $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		// $processDefinition = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process authoring service');
		// if($processDefinition instanceof core_kernel_classes_Resource){
			// $this->proc = $processDefinition;
		// }
	}
	
	public function testService(){
		
		$deliveryService = tao_models_classes_ServiceFactory::get('Delivery');
		$this->assertIsA($deliveryService, 'tao_models_classes_Service');
		$this->assertIsA($deliveryService, 'taoDelivery_models_classes_DeliveryService');
		
		$this->deliveryService = $deliveryService;
	}
	
	public function testCreateInstance(){
		$delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery1');
		
		//check if a process is associated to the delivery content:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//check if the default delivery server exists:
		$defaultDeliveryServer = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
		$this->assertNotNull($defaultDeliveryServer);
		
		if(!is_null($defaultDeliveryServer)){
			$this->assertEqual( (string) $defaultDeliveryServer->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_URL_PROP)), 'localhost');
		}
		
		$this->delivery = $delivery;
		
		
	}
	
	public function testSetDeliveryTests(){
	
		//create 2 tests:
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testClass->createInstance('UnitDelivery Test1', 'Test 1 create for delivery unit test');
		$test2 = $testClass->createInstance('UnitDelivery Test2', 'Test 2 create for delivery unit test');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
		
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		
		//analyze the content of the process:
		$process = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//count the number of activity:
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		$activities = $authoringService->getActivitiesByProcess($process);
		$this->assertEqual(count($activities), 2);
		foreach($activities as $activity){
			//check if an interactive is set and that it is the test
			$service = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			$this->assertNotNull($service);
			if(!is_null($service)){
				$serviceDefinition = $service->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
				$formalParamCollection = $serviceDefinition->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN));
				$this->assertEqual($formalParamCollection->count(), 3);
			}
		}
		
		$test1->delete();
		$test2->delete();
	}
	
	public function testCompileTest(){
		// require_once(ROOT_PATH . '/taoTests/includes/constants.php');
		$testContentRefPath = ROOT_PATH .'/taoTests/data/test_content_ref.xml';
		// var_dump($testContentRefPath, TEST_CONTENT_REF_FILE);
		$this->assertTrue(file_exists($testContentRefPath));
		define('TEST_CONTENT_REF_FILE', $testContentRefPath);//explicitely define here, because required in testsService:
		define('TAO_ITEM_CONTENT_PROPERTY', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent');
		
		//items sample:
		$xmlCTest = "<?xml version='1.0' encoding='UTF-8'?>
					<tao:ITEM xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' rdf:ID='#i1234567' xmlns:tao='http://www.tao.lu/tao.rdfs' xmlns:rdfs='http://www.w3.org/2000/01/rdf-schema#'>
						<rdfs:LABEL lang='EN'>CTest Item</rdfs:LABEL>
						<rdfs:COMMENT lang='EN'></rdfs:COMMENT>
						<CTInfos>
							<Text>The TAO project is an Open Source Computer Based Asses{sment} Platform. The TAO pla{tform} provides to all the actors of the ent{ire} computer-based assessment process a comprehensive set of functionalities enabling the creation, the manage{ment}, and the delivery of electronic assessments.</Text>
							<Timer>30</Timer>
							<Words></Words>
							<Ports></Ports>
							<Coords></Coords>
							<Undo></Undo>
						</CTInfos>
					</tao:ITEM>";
		
		$xmlQCM = "	
			<?xml version='1.0' encoding='UTF-8' ?>
			<tao:ITEM xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' rdf:ID='#123456' xmlns:tao='http://www.tao.lu/tao.rdfs#' xmlns:rdfs='http://www.w3.org/TR/1999/PR-rdf-schema-19990303#'>".
			'<rdfs:LABEL lang="EN">QCM Item</rdfs:LABEL>
			<rdfs:COMMENT lang="EN"></rdfs:COMMENT>
			<tao:DISPLAYALLINQUIRIES></tao:DISPLAYALLINQUIRIES>
			<tao:DURATION></tao:DURATION>
			<tao:ITEMPRESENTATION>
				<xul>
					<stylesheet id="item_stylesheet" src="./item.css"/>
					<box id="itemContainer_box" class="item">
						<label id="problem_textbox" left="5" top="50" multiline="true" wrap="true" value="&lt;p&gt;&lt;img src=&quot;https://www.tao.lu/html/images/stories/download/bt_tao_1.0_110x32px.png&quot; /&gt;&lt;br /&gt;&lt;br /&gt;Hello, this is a &lt;i&gt;simple &lt;/i&gt;sample of a &lt;font size=&quot;12&quot; color=&quot;#ff0000&quot;&gt;QCM &lt;/font&gt;Item. &lt;br /&gt;It offer you different set of features to build your Items. &lt;br /&gt; &lt;br /&gt;"/><image src="https://www.tao.lu/html/images/stories/download/bt_tao_1.0_110x32px.png" />
						<button id="prevInquiry_button" left="45" top="300" label="&lt;" image="http://localhost/filemanager/views/data/nav/go-previous.png" url="http://localhost/filemanager/views/data/nav/go-previous.png" disabled="true" oncommand="tao_item.prevInquiry"/>
						<button id="nextInquiry_button" left="100" top="300" label="&gt;" image="http://localhost/filemanager/views/data/nav/go-next.png" url="http://localhost/filemanager/views/data/nav/go-next.png" disabled="true" oncommand="tao_item.nextInquiry"/>
					   <!--<progressmeter id="item_progressmeter" left="160" top="300" mode="determined" value="0"/>-->
						<box id="inquiryContainer_box" left="5" top="100"/>
					</box>
				</xul>
			</tao:ITEMPRESENTATION>
			<tao:ITEMLISTENERS></tao:ITEMLISTENERS>
			<tao:PROBLEM lang="EN" type="String">&lt;p&gt;&lt;img src=&quot;https://www.tao.lu/html/images/stories/download/bt_tao_1.0_110x32px.png&quot; /&gt;&lt;br /&gt;&lt;br /&gt;Hello, this is a &lt;i&gt;simple &lt;/i&gt;sample of a &lt;font size=&quot;12&quot; color=&quot;#ff0000&quot;&gt;QCM &lt;/font&gt;Item. &lt;br /&gt;It offer you different set of features to build your Items. &lt;br /&gt; &lt;br /&gt;</tao:PROBLEM>
			<tao:INQUIRY order="1">
				<tao:QUESTION lang="EN" type="String">What type of Item is the most recent in TAO ?</tao:QUESTION>
				<tao:INQUIRYDESCRIPTION>
					<tao:PROPOSITIONTYPE>Exclusive Choice</tao:PROPOSITIONTYPE>
					<tao:WIDGET>FLASH Radio Button</tao:WIDGET>
					<tao:PROPLISTENERS>
						<tao:ITEMBEHAVIOR tao:LISTENERNAME="Answered : What type of Item is the most recent in TAO ?" src="#{XPATH(/tao:ITEM/tao:INQUIRY[@order=1]/tao:INQUIRYDESCRIPTION/tao:HASPRESENTATIONLAYER/xul/box/box/radiogroup)}#"/>
					</tao:PROPLISTENERS>
					<tao:ANSWERTYPE>Exclusive Vector</tao:ANSWERTYPE>
					<tao:EVALUATIONRULE>AND.swf</tao:EVALUATIONRULE>
					<tao:HASGUIDE>technicalID.hlp</tao:HASGUIDE>
					<tao:HASPRESENTATIONLAYER>
						<xul>
							<box id="inquiryContainer_box" left="0" top="0">
							<textbox id="question_textbox" wrap="true" style="borderStyle:none" readonly="true" width="700" height="45" left="0" top="0" class="question" value="What type of Item is the most recent in TAO ?" />
								<box id="propositions_box" left="10" top="13">
									<radiogroup id="propositions_radiogroup">
									<radio id="proposition_1_radio" left="5" top="30" width="1000" selected="false" label="QCM Items"></radio>
									<radio id="proposition_2_radio" left="5" top="50" width="1000" selected="false" label="Khos Items"></radio>
									<radio id="proposition_3_radio" left="5" top="70" width="1000" selected="false" label="C-test Items"></radio>
									<radio id="proposition_4_radio" left="5" top="90" width="1000" selected="false" label="HAWAI Items"></radio>
									</radiogroup>
								</box>
							</box>
						</xul>
					</tao:HASPRESENTATIONLAYER>
					
					<tao:LISTPROPOSITION>
						<tao:PROPOSITION lang="EN" type="String" Id="1" order="1" answer="0">QCM Items</tao:PROPOSITION>
						<tao:PROPOSITION lang="EN" type="String" Id="2" order="2" answer="0">Khos Items</tao:PROPOSITION>
						<tao:PROPOSITION lang="EN" type="String" Id="3" order="3" answer="0">C-test Items</tao:PROPOSITION>
						<tao:PROPOSITION lang="EN" type="String" Id="4" order="4" answer="0">HAWAI Items</tao:PROPOSITION>
					</tao:LISTPROPOSITION>
					<tao:HASANSWER>0001</tao:HASANSWER>
				</tao:INQUIRYDESCRIPTION>
			</tao:INQUIRY>
			
			<tao:INQUIRY order="2">
				<tao:QUESTION lang="EN" type="String">What is an &amp;quot;HAWAI&amp;quot; item?</tao:QUESTION>
				<tao:INQUIRYDESCRIPTION><tao:PROPOSITIONTYPE>Exclusive Choice</tao:PROPOSITIONTYPE>
				<tao:WIDGET>FLASH Radio Button</tao:WIDGET>
				<tao:PROPLISTENERS><tao:ITEMBEHAVIOR tao:LISTENERNAME="Answered : What is an &amp;quot;HAWAI&amp;quot; item?" src="#{XPATH(/tao:ITEM/tao:INQUIRY[@order=2]/tao:INQUIRYDESCRIPTION/tao:HASPRESENTATIONLAYER/xul/box/box/radiogroup)}#"/></tao:PROPLISTENERS>
				<tao:ANSWERTYPE>Exclusive Vector</tao:ANSWERTYPE>
				<tao:EVALUATIONRULE>AND.swf</tao:EVALUATIONRULE>
				<tao:HASGUIDE>technicalID.hlp</tao:HASGUIDE>
				<tao:HASPRESENTATIONLAYER>
					<xul>
						<box id="inquiryContainer_box" left="0" top="0">
							<textbox id="question_textbox" wrap="true" style="borderStyle:none" readonly="true" width="700" height="45" left="0" top="0" class="question" value="What is an &amp;quot;HAWAI&amp;quot; item?" />
							<box id="propositions_box" left="10" top="13">
								<radiogroup id="propositions_radiogroup">
									<radio id="proposition_1_radio" left="5" top="0" width="1000" selected="false" label="The name of the item&#039;s designed for the US island of Hawai"></radio>
									<radio id="proposition_2_radio" left="5" top="10" width="1000" selected="false" label="The kind of item too much adaptive and highly customizable"></radio>
								</radiogroup>
							</box>
						</box>
					</xul>
				</tao:HASPRESENTATIONLAYER>
				<tao:LISTPROPOSITION>
					<tao:PROPOSITION lang="EN" type="String" Id="1" order="1" answer="0">The name of the item&#039;s designed for the US island of Hawai</tao:PROPOSITION>
					<tao:PROPOSITION lang="EN" type="String" Id="2" order="2" answer="0">The kind of item too much adaptive and highly customizable</tao:PROPOSITION>
				</tao:LISTPROPOSITION>
				<tao:HASANSWER>01</tao:HASANSWER>
			</tao:INQUIRYDESCRIPTION>
			</tao:INQUIRY>
		</tao:ITEM>';
		
		$xmlKohs = '<?xml version="1.0" encoding="UTF-8"?>
			<tao:KOHS xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" rdf:Id="#123456" xmlns:tao="http://www.tao.lu/tao.rdfs#" xmlns:rdfs="http://www.w3.org/TR/1999/PR-rdf-schema-19990303#">
				<tao:MATRIX>152666354</tao:MATRIX>
			</tao:KOHS>';
		
		$xmlHAWAI = '<?xml version="1.0" encoding="UTF-8"?>
			<black:Manifest 
				xmlns:black="http://www.exulis.lu/black.rdfs#" 
				xmlns:rdfs="http://www.w3.org/TR/1999/PR-rdf-schema-19990303#" 
				xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
				xmlns:tao="http://www.tao.lu/tao.rdfs#" 
				rdf:ID="http://www.tao.lu/Ontologies/TAOItem.rdf#i1261571812010328500" 
			>
				<!-- @see taoItems_models_classes_ItemsService::getAuthoringFile for URI/FILE resolution -->
				<root reference="http://www.tao.lu/Ontologies/TAOItem.rdf#i1261571812010328500" />
			</black:Manifest>';
			
		//create a full delivery, with real tests and items:
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$item1 = $itemClass->createInstance('UnitDelivery Item1', 'Item 1 create for delivery unit test');
		$item2 = $itemClass->createInstance('UnitDelivery Item2', 'Item 2 create for delivery unit test');
		$item3 = $itemClass->createInstance('UnitDelivery Item3', 'Item 3 create for delivery unit test');
		$item4 = $itemClass->createInstance('UnitDelivery Item4', 'Item 4 create for delivery unit test');
		
		$itemContentProp = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		$item1->editPropertyValues($itemContentProp, $xmlCTest);				
		$item2->editPropertyValues($itemContentProp, $xmlCTest);//$xmlQCM problem with img file copying
		$item3->editPropertyValues($itemContentProp, $xmlKohs);
		$item4->editPropertyValues($itemContentProp, $xmlHAWAI);
		
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testClass->createInstance('UnitDelivery Test1', 'Test 1 create for delivery unit test');
		
		//assign the items to the tests: 
		// $itemService = tao_models_classes_ServiceFactory::get('Items');
		// $this->assertIsA($itemsService, 'tao_models_classes_Service');
		// $this->assertIsA($itemsService, 'taoItems_models_classes_ItemsService');
		$testsService = tao_models_classes_ServiceFactory::get('Tests');
		$this->assertIsA($testsService, 'tao_models_classes_Service');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		$testsService->setRelatedItems($test1, array($item1->uriResource, $item2->uriResource, $item3->uriResource), true);
		
		//set tests to active to allow compilation:
		$test1->editPropertyValues(new core_kernel_classes_Property(TEST_ACTIVE_PROP), GENERIS_TRUE);
		
		//execute compilation:
		$results = $this->deliveryService->compileTest($test1->uriResource);
		$this->assertEqual($results['success'], 1);
		if($results['success'] != 1){
			var_dump($results);
		}
		
		//check create folder and files:
		$compilator = new taoDelivery_helpers_Compilator($test1->uriResource);//new constructor
		$compiledPath = $compilator->getCompiledPath();
		// var_dump($compiledPath);
		$this->assertTrue(is_dir($compiledPath));
		$this->assertTrue(file_exists($compiledPath.'Test.xml'));
		$this->assertTrue(file_exists($compiledPath.'Test.swf'));
		$this->assertTrue(file_exists($compiledPath.'theTest.php'));
		
		//destroy compile folder contents:
		$this->assertTrue($compilator->clearCompiledFolder());
		$this->assertFalse(file_exists($compiledPath.'Test.xml'));
		
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$test1->delete();
	}
	
	public function testDeleteDelivery(){
		$this->deliveryService->deleteDelivery($this->delivery);
		//check if the process has been deleted:
		$this->assertNull($this->delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT)));
	}
}

