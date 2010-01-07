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
 * The taoDelivery_models_classes_DeliveryService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_ResultServerService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The attribute resultServerClass contains the default TAO Delivery Class
     *
     * @access protected
     * @var Class
     */
    protected $resultServerClass = null;
	
    /**
     * The attribute deliveryOntologies contains the reference to the TAODelivery Ontology
     *
     * @access protected
     * @var array
     */
    protected $deliveryOntologies = array(
		'http://www.tao.lu/Ontologies/TAODelivery.rdf',
		'http://www.tao.lu/Ontologies/TAOGroup.rdf',
		'http://www.tao.lu/Ontologies/TAOSubject.rdf',
		'http://www.tao.lu/Ontologies/TAOTest.rdf'
		);
	
	
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
		
		$this->resultServerClass = new core_kernel_classes_Class(TAO_DELIVERY_RESULTSERVER_CLASS);
		$this->loadOntologies($this->deliveryOntologies);
    }
	
	/**
     * The method getResultServerClass return the current ResultServer Class
	 * (not used yet in the current implementation)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getResultServerClass($uri = '')
    {
        $returnValue = null;

		if(empty($uri) && !is_null($this->resultServerClass)){
			$returnValue = $this->resultServerClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isResultServerClass($clazz)){
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
    public function getResultServer($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->resultServerClass;
		}
		if($this->isResultServerClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		
        return $returnValue;
    }
	
	 /**
     * Create a new subclass of ResultServer, which is basically always a subclass of an existing ResultServer class.
	 * Require an array('propertyName' => 'propertyValue')
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createResultServerClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->resultServerClass;
		}
		
		if($this->isResultServerClass($clazz)){
		
			$resultServerClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $resultServerClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' resultServer property from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
			}
			$returnValue = $resultServerClass;
		}

        return $returnValue;
    }
	
	/**
     * Method to be called to delete a resultServer instance
     * (Method is not used in the current implementation yet)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource resultServer
     * @return boolean
     */
    public function deleteResultServer( core_kernel_classes_Resource $resultServer)
    {
        $returnValue = (bool) false;
		
		if(!is_null($resultServer)){
			$returnValue = $resultServer->delete();
		}

        return (bool) $returnValue;
    }

    /**
     * Method to be called to delete a resultServer class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function deleteResultServerClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if(!is_null($clazz)){
			if($this->isResultServerClass($clazz) && $clazz->uriResource != $this->resultServerClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Check whether the object is a resultServer class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function isResultServerClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($clazz->uriResource == $this->resultServerClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->resultServerClass->getSubClasses(true) as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}

        return (bool) $returnValue;
    }
			
    /**
     * get the list of deliveries in the resultServer in parameter
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource resultServer
     * @return array
     */
    public function getRelatedDeliveries( core_kernel_classes_Resource $resultServer){
        $returnValue = array();
		
		if(!is_null($resultServer)){
		
			$deliveries = core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_RESULTSERVER_PROP, $resultServer->uriResource);
			foreach ($deliveries->getIterator() as $delivery){
				if($delivery instanceof core_kernel_classes_Resource ){
					$returnValue[] = $delivery->uriResource;
				}
			}
		}

        return (array) $returnValue;
    }

    /**
     * define the list of tests composing a resultServer
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource resultServer
     * @param  array deliveries
     * @return boolean
     */
    public function setRelatedDeliveries( core_kernel_classes_Resource $resultServer, $deliveries = array())
    {
        $returnValue = (bool) false;
		
		if(!is_null($resultServer)){
			//the property of the DELIVERIES that will be modified
			$resultServerProp = new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP);
			
			//a way to remove the resultServer property value of the delivery that are used to be associated to THIS resultServer
			$oldRelatedDeliveries = core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_RESULTSERVER_PROP, $resultServer->uriResource);
			foreach ($oldRelatedDeliveries->getIterator() as $oldRelatedDelivery) {
				//TODO check if it is a delivery instance
				
				//find a way to remove the property value associated to THIS resultServer ONLY
				$remove = core_kernel_classes_ApiModelOO::singleton()->removeStatement($oldRelatedDelivery->uriResource, TAO_DELIVERY_RESULTSERVER_PROP, $resultServer->uriResource, '');
				// $this->assertTrue($remove);
				
				// $oldRelatedDelivery->removePropertyValues($resultServerProp);//issue with this implementation: delete all property values
			}
			
			//assign the current compaign to the selected deliveries	
			$done = 0;
			foreach($deliveries as $delivery){
				//the delivery instance to be modified
				$deliveryInstance=new core_kernel_classes_Resource($delivery);
			
				//remove the property value associated to another delivery in case ONE delivery can ONLY be associated to ONE resultServer
				//if so, then change the widget from comboBox to treeView in the delivery property definition
				$deliveryInstance->removePropertyValues($resultServerProp);
				
				//now, truly assigning the resultServer uri to the affected deliveries
				if($deliveryInstance->setPropertyValue($resultServerProp, $resultServer->uriResource)){
					$done++;
				}
			}
			if($done == count($deliveries)){
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }

} /* end of class taoDelivery_models_classes_ResultServerService */

?>