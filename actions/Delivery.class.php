<?php
require_once('tao/actions/CommonModule.class.php');


class Delivery extends CommonModule {
	
	public function __construct(){
		
		//parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Delivery');
		$this->defaultData();
	}

	public function index(){
	/*
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
	}
	
	public function addDelivery(){
		
		
	}
	

}
?>