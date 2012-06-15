<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
return array(
	'name' => 'taoDelivery',
	'description' => 'TAO http://www.tao.lu',
	'additional' => array(
		'version' => '2.3',
		'author' => 'CRP Henri Tudor',
		'dependances' => array('wfEngine'),
		'extends' => 'tao',
		'models' => array('http://www.tao.lu/Ontologies/TAODelivery.rdf',
			'http://www.tao.lu/Ontologies/taoFuncACL.rdf'),
		'install' => array(
			'rdf' => array(
					array('ns' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf', 'file' => dirname(__FILE__). '/models/ontology/taodelivery.rdf'),
					array('ns' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf', 'file' => dirname(__FILE__). '/models/ontology/coding.rdf'),
			)
		),
		'classLoaderPackages' => array(
			dirname(__FILE__).'/actions/',
			dirname(__FILE__).'/helpers/'

		 )
	)
);
?>