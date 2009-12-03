<?php
$todefine = array(
	'TAO_DELIVERY_CLASS' => 'http://127.0.0.1/middleware/demoDelivery.rdf#125966968758554',
	'TAO_SUBJECT_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_SUBJECT_NAMESPACE' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf',
	'LOCAL_NAMESPACE' 		=> 'http://127.0.0.1/middleware/demoSubjects.rdf',
	'TAO_OBJECT_CLASS'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject',
	'TEST_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'TEST_RELATED_ITEMS_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems',
	'TEST_TESTCONTENT_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	'ITEM_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>
