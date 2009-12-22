<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every service instances.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('tao/models/classes/class.Service.php');

/**
 * The Precompilator class provides many useful methods to accomplish the test compilation task
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('taoDelivery/helpers/class.Precompilator.php');

/**
 * The taoDelivery_models_classes_DeliveryService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_DeliveryService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The attribute deliveryClass contains the default TAO Delivery Class
     *
     * @access protected
     * @var Class
     */
    protected $deliveryClass = null;

	/**
     * The attribute testClass contains the default TAO Test Class
     *
     * @access protected
     * @var Class
     */
	protected $testClass = null;
	
	/**
     * The attribute subjectClass contains the default TAO Subject Class
     *
     * @access protected
     * @var Class
     */
	protected $subjectClass = null;
	
	/**
     * The attribute groupClass contains the default TAO Group Class
     *
     * @access protected
     * @var Class
     */
	protected $groupClass = null;
		
    /**
     * The attribute deliveryOntologies contains the reference to the TAODelivery Ontology
     *
     * @access protected
     * @var array
     */
    protected $deliveryOntologies = array('http://www.tao.lu/Ontologies/TAODelivery.rdf');
	
	/**
     * The attribute groupsOntologies contains the reference to the TAOGroup Ontologies.
	 * It enables the DeliveryService Class to connect to them and fetch information about groups from them
     *
     * @access protected
     * @var array
     */
	protected $groupsOntologies = array('http://www.tao.lu/Ontologies/TAOGroup.rdf');
	
	/**
     * The attribute groupsOntologies contains the reference to the TAOGroup Ontologies.
	 * It enables the DeliveryService Class to connect to them and fetch information about groups from them
     *
     * @access protected
     * @var array
     */
	protected $subjectsOntologies = array('http://www.tao.lu/Ontologies/TAOSubject.rdf');
	
	/**
     * The attribute groupsOntologies contains the reference to the TAOGroup Ontologies.
	 * It enables the DeliveryService Class to connect to them and fetch information about groups from them
     *
     * @access protected
     * @var array
     */
	protected $testsOntologies = array('http://www.tao.lu/Ontologies/TAOTest.rdf');
	
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
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
	
	/**
     * The method getDeliveryClass return the current Delivery Class
	 * (not used yet in the current implementation)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string uri
     * @return core_kernel_classes_Class
     */
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
     * The method getGroupClass return the current Test Class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string uri
     * @return core_kernel_classes_Class
     */
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
     * Returns a delivery by providing either its uri (default) or its label and the delivery class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
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
     * Create a new class of Delivery, which is basically always a subclass of an existing Delivery class.
	 * Require an array('propertyName' => 'propertyValue')
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
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
			}
			$returnValue = $deliveryClass;
		}

        return $returnValue;
    }
	
	/**
     * Method to be called to delete a delivery instance
     * (Method is not used in the current implementation yet)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return boolean
     */
    public function deleteDelivery( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;
		
		if(!is_null($delivery)){
			$returnValue = $delivery->delete();
		}

        return (bool) $returnValue;
    }

    /**
     * Method to be called to delete a delivery class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
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
     * Check whether the object is a delivery class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
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
	
	/**
     * Check whether the object is a test class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
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
     * Check the login/pass in a MySQL table to identify a subject when he/she takes the delivery.
     * This method is used in the Delivery Server and uses direct access to the database for performance purposes.
	 * It returns the uri of the identified subjectm and an empty string otherwise.
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string login
	 * @param  string password
     * @return string
     */
	public function checkSubjectLogin($login, $password){
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
	}
	
	/**
     * Get all tests available for the identified subject.
     * This method is used in the Delivery Server and uses direct access to the database for performance purposes.
	 * It returns an array containing the uri of selected tests or an empty array otherwise.
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string subjectUri
     * @return array
     */
	public function getTestsBySubject($subjectUri){
		
		$returnValue=array();
		
		$db = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
		$query = "SELECT s2.object FROM statements AS s1, statements AS s2
			WHERE s1.subject=s2.subject  
			AND s1.object='$subjectUri'
			AND s1.predicate='http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'
			AND s2.predicate='http://www.tao.lu/Ontologies/TAOGroup.rdf#Tests'";
		
		$result = $db->execSql($query);
		while(!$result->EOF) {
			$returnValue[]=$result->fields["object"];
			$result->MoveNext();
		}
		
		return $returnValue;
	}
	
	/**
     * The methods getTestStatus checks the value of the property "active" OR "compiled" for a given test instance (a ressource)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource aTestInstance
	 * @param  string status
     * @return boolean
     */
	public function getTestStatus($aTestInstance, $status){
		
		$returnValue=false;
		
		if(!($aTestInstance instanceof core_kernel_classes_Resource) ){
			throw new Exception("wrong resource in getTestStatus parameter");
			return $returnValue;
		}
		
		switch($status){
			case "active":
				$property=TEST_ACTIVE_PROP;
				break;
				
			case "compiled":
				$property=TEST_COMPILED_PROP;
				
				//check if the compiled folder exists:
				/*
				$testId=tao_helpers_Precompilator::getUniqueId($aTestInstance->uriResource);
				
				if(!is_dir(BASE_PATH."/compiled/$testId/")){
					return $returnValue;
				}*/ 
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