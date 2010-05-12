<?php
	return array(
		'name' => 'TAO Delivery',
		'description' => 'TAO http://www.tao.lu',
		'additional' => array(
			'version' => '1.0',
			'author' => 'CRP Henri Tudor',
			'dependances' => array(),
			'install' => array( 
				'sql' => dirname(__FILE__). '/model/ontology/TAODelivery.sql',
				'php' => dirname(__FILE__). '/install/install.php'
			),
			
			'model' => array( 
							'http://www.tao.lu/Ontologies/TAODelivery.rdf',
							'http://www.tao.lu/Ontologies/TAOGroup.rdf',
							'http://www.tao.lu/Ontologies/TAOSubject.rdf',
							'http://www.tao.lu/Ontologies/TAOTest.rdf'
			),
		
			'classLoaderPackages' => array( 
				dirname(__FILE__).'/actions/',
				dirname(__FILE__).'/helpers/'

			 )	
			
		)
	);
?>