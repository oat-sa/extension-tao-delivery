<?php

error_reporting(E_ALL);


if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

require_once('tao/models/classes/class.Service.php');
require_once('taoDelivery/helpers/class.Precompilator.php');

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
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
    //protected $deliveryClass = null;

	protected $testClass = null;
	
	protected $subjectClass = null;
	
	protected $groupClass = null;
		
    /**
     * Short description of attribute deliveryOntologies
     *
     * @access protected
     * @var array
     */
    //protected $deliveryOntologies = array('http://www.tao.lu/Ontologies/TAODelivery.rdf');
	
	protected $groupsOntologies = array('http://www.tao.lu/Ontologies/TAOGroup.rdf');
	
	protected $subjectsOntologies = array('http://www.tao.lu/Ontologies/TAOSubject.rdf');
	
	protected $testsOntologies = array('http://www.tao.lu/Ontologies/TAOTest.rdf');
	
    // --- OPERATIONS ---

    public function __construct()
    {
		parent::__construct();
		// $this->deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		// $this->loadOntologies($this->deliveryOntologies);
		
		$this->testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$this->subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
		$this->groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		
		$this->loadOntologies(array(
			$this->testsOntologies,
			$this->subjectsOntologies,
			$this->groupsOntologies
			));
    }
	
	//hypothesis: being able to access the external ontology 'subjects'
	public function getSubjectInstances(){
	
		$instancesData = array();
		
		//connect to the class : 'TAO_SUBJECT_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject'
		$clazz = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#125187560431708');
		$instancesData=$this->getAllInstances($clazz);
		
		return $instancesData;
	}
	
	public function getTestInstances(){}
	
	/**
	*more general version of getInstances method, applicable for Delivery, Subjects and Tests
	*/
	public function getAllInstances(core_kernel_classes_Class $clazz = null){
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
	
	public function getTestClass($uri = '')
    {
        $returnValue = null;

		if(empty($uri) && !is_null($this->testClass)){
			$returnValue = $this->testClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isTestClass($clazz)){
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
	*Return all instances of a class of Delivery, equivalent de ce qui existe deja dans Groups comme la methode getGroups par exemple
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
	
	public function isTestClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($clazz->uriResource == $this->testClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->testClass->getSubClasses() as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}

        return (bool) $returnValue;
    }
	
	
	/**
     * check login/pass in a MySQL table to identify subjects when they will be taking the tests.
     */
	public function checkSubjectLogin($login,$password){
		//http://www.tao.lu/Ontologies/TAOSubject.rdf#Login
		//http://www.tao.lu/Ontologies/TAOSubject.rdf#Password
		$returnValue='';
		
		$db = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
		$query = "SELECT s1.subject FROM statements AS s1, statements AS s2
			WHERE s1.subject=s2.subject
			AND s1.predicate='".SUBJECT_LOGIN_PROP."'
			AND s1.object='$login'
			AND s2.predicate='".SUBJECT_PASSWORD_PROP."'
			AND	s2.object='$password'";
		
		$result = $db->execSql($query);
		if(!$result->EOF) {
			$returnValue=$result->fields["subject"];
		}

		return $returnValue;
		//empty or not?	
	}
	
	public function getTestsBySubject($subjectUri){
		//http://www.tao.lu/Ontologies/TAOGroup.rdf#Group
		//http://www.tao.lu/Ontologies/TAOGroup.rdf#Members
		//http://www.tao.lu/Ontologies/TAOGroup.rdf#Tests
		
		$db = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
		$query = "SELECT s2.object FROM statements AS s1, statements AS s2
			WHERE s1.subject=s2.subject  
			AND s1.object='$subjectUri'
			AND s1.predicate='http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'
			AND s2.predicate='http://www.tao.lu/Ontologies/TAOGroup.rdf#Tests'";
		
		$query = "SELECT s2.object FROM statements AS s1, statements AS s2
			WHERE s1.subject=s2.subject  
			AND s1.object='$subjectUri'
			AND s1.predicate='http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'
			AND s2.predicate='http://www.tao.lu/Ontologies/TAOGroup.rdf#Tests'";
			
		$result = $db->execSql($query);
		
		return $result;
		
		//an array
	}
	
	//check either the value of the properties "active" or "compiled" for a given test instance (a ressource)
	//assumption: the status active or compiled is not language dependent
	public function getTestStatus($aTestInstance, $status){
		$returnValue=false;
		
		switch($status){
			case "active":
				$property=TEST_ACTIVE_PROP;
				break;
			case "compiled":
				$property=TEST_COMPILED_PROP;
				//check if the compiled folder exists
				$testId=tao_helpers_Precompilator::getUniqueId($aTestInstance->uriResource);
				if(!is_dir("../../compiled/$testId/")){
					return $returnValue;
				}
				break;
			default:
				throw new Exception("wrong test status parameter");
				return $returnValue;
		}
		
		foreach ($aTestInstance->getPropertyValuesCollection(new core_kernel_classes_Property($property))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Resource ){
				if ($value->uriResource == GENERIS_TRUE){
					$returnValue=true;
				}	
			}
		}
		
		return $returnValue;
	}

} /* end of class taoGroups_models_classes_DeliveryService */

?>