<?php

error_reporting(E_ALL);


if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

require_once('tao/models/classes/class.Service.php');
require_once('taoTests/models/classes/class.TestsService.php');
require_once('taoSubjects/models/classes/class.SubjectsService.php');

class taoDelivery_models_classes_DeliveryService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute deliveryClass
     *
     * @access protected
     * @var Class
     */
    protected $deliveryClass = null;

	protected $testClass = null;
	
	protected $subjectClass = null;
	
	protected $subjectService = null;
	
    /**
     * Short description of attribute deliveryOntologies
     *
     * @access protected
     * @var array
     */
    protected $deliveryOntologies = array('http://www.tao.lu/Ontologies/TAODelivery.rdf');
	
    // --- OPERATIONS ---

    public function __construct()
    {
		parent::__construct();
		$this->deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		$this->loadOntologies($this->deliveryOntologies);
		
		//create instances of the tests and subjects model to retrieve information from the tests and subjects extensions.
		//the controller Delivery.class.php will use available method to fetch information from tests and subjects ontologies.
		$this->testClass = new taoTests_models_classes_TestsService();
		$this->subjectService = tao_models_classes_ServiceFactory::get('Subjects');// ne fonctionne pas
    }
	
	public function getSubjectInstances(){
		$allInstances=$this->subjectService->getSubjects();
		var_dump(json_encode( ($allInstances) ));
	}
	
    public function getDeliveryClass($uri = '')
    {
        $returnValue = null;

		if(empty($uri) && !is_null($this->deliveryClass)){
			$returnValue = $this->deliveryClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isDeliveryClass($clazz)){
				$returnValue = $clazz;
			}
		}

        return $returnValue;
    }
	
	/**
	*
	*Return a delivery by providing either its uri (default) or its label and the class of delivery
	*/
     public function getDelivery($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->groupClass;
		}
		if($this->isGroupClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		
        return $returnValue;
    }
	
	/**
	*
	*Return all instances of a class of Delivery, equivalent de ce qui existe deja dans Groups conne la methode getGroups par exemple
	*/
	public function getAllDeliveries(core_kernel_classes_Class $clazz = null){
		$instancesData = array();
		foreach($clazz->getInstances(false) as $instance){
			$instancesData[] = array(
				'data' 	=> tao_helpers_Display::textCutter($instance->getLabel(), 16),
				'attributes' => array(
					'id' => tao_helpers_Uri::encode($instance->uriResource),
					'class' => 'node-instance'
				),
				'properties' => $this->getProperties($clazz,true)
			);
		}
		return $instancesData;
	}
	
	//utiliser service::createInstance($classOfDelivery, $label="") et service::bindProperty() à la place pour créer des instances
	public function createDelivery($label='', $comment=''){
        $returnValue = null;

		$class = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		$returnValue = $class->createInstance($label , $comment);
		//http://127.0.0.1/middleware/demoDelivery.rdf#125966997638460 est l uri pour la ressource de la prop 1 maxexec
		$maxexec = new core_kernel_classes_Property('http://127.0.0.1/middleware/demoDelivery.rdf#125966997638460'); 	
		$returnValue->setPropertyValue($maxexec,"6");
		
        return $returnValue;
    }
	
	/**
	*return all properties+values of an instance of a class
	*/
	public function getProperties(core_kernel_classes_Class $instance = null, $debug=false){
//		$clazz = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
//		var_dump($class->getAllProperties());
		$properties=array();
		foreach ($instance->getAllProperties()->getIterator() as $prop) {
//			var_dump($prop->uriResource,$property->uriResource);
			$collection = $instance->getPropertyValuesCollection($prop);
			$propertyValue='';
			foreach ($collection->getIterator() as $value) {
				//en supposant que ( $value instanceof core_kernel_classes_Literal) et non ($value instanceof core_kernel_classes_Resource )
				var_dump($value);
				$propertyValue .= $value->literal.'('.$value->uriResource.'), ';						
			}
			$propertyValue = substr_replace($propertyValue,'',-2);
			
			if($debug){
				//$rdfTriples=$prop->getRdfTriples();
				$properties[]=array("label"=>$prop->getLabel(),
									"value"=>$propertyValue);
			}else{
				$properties[]=array("label"=>$prop->getLabel(),
									"value"=>$propertyValue);
			}
		}
		return $properties;
	}

    /**
     * Create a new class of Delivery, which is basically always a subclass of an existing Delivery class.
	 * Require an array('propertyName' => 'propertyValue')
     */
    public function createDeliveryClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->deliveryClass;
		}
		
		if($this->isDeliveryClass($clazz)){
		
			$deliveryClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $deliveryClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' subject property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
				
				//@todo implement check if there is a widget key and/or a range key
			}
			$returnValue = $deliveryClass;
		}

        return $returnValue;
    }

    public function deleteDelivery( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;
		
		if(!is_null($delivery)){
			$returnValue = $group->delivery();
		}

        return (bool) $returnValue;
    }

    /**
     * Delete a class of Delivery
     */
    public function deleteDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if(!is_null($clazz)){
			if($this->isDeliveryClass($clazz) && $clazz->uriResource != $this->deliveryClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Check whether the class is a deliveryClass
     */
    public function isDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($clazz->uriResource == $this->deliveryClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->deliveryClass->getSubClasses() as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}

        return (bool) $returnValue;
    }

} /* end of class taoGroups_models_classes_GroupsService */

?>