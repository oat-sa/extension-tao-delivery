<?php
$todefine = array(
	'TAO_DELIVERY_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery',
	'TAO_SUBJECT_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_GROUP_CLASS' => 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'TAO_TEST_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'LOCAL_NAMESPACE' 		=> 'http://127.0.0.1/middleware/demo.rdf',
	'TEST_RELATED_ITEMS_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems',
	'TEST_TESTCONTENT_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	'TEST_COMPILED_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#compiled',
	'TEST_ACTIVE_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#active',
	'ITEM_ITEMCONTENT_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent',
	'ITEM_ITEMMODEL_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel',
	'ITEM_MODEL_RUNTIME_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile', 
	'SUBJECT_LOGIN_PROP' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Login',
	'SUBJECT_PASSWORD_PROP' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Password',
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_MODEL_WATERPHENIX'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#125933161031263',
	'TAO_ITEM_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'TAO_ITEM_AUTHORING_BASE_URI' 		=> $_SERVER['DOCUMENT_ROOT'].'/taoItems/data',
	'TAO_ITEM_AUTHORING_TPL_FILE' 		=> $_SERVER['DOCUMENT_ROOT'].'/taoItems/data/black_ref.xml',
	'GENERIS_BOOLEAN'		=> 'http://www.tao.lu/Ontologies/generis.rdf#Boolean'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>
