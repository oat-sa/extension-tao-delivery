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
 * The taoDelivery_models_classes_CampaignService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_CampaignService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The attribute campaignClass contains the default TAO Campaign Class
     *
     * @access protected
     * @var Class
     */
    protected $campaignClass = null;
	
    /**
     * The attribute deliveryOntologies contains the reference to the TAODelivery Ontology
     *
     * @access protected
     * @var array
     */
    protected $deliveryOntologies = array(
		'http://www.tao.lu/Ontologies/TAODelivery.rdf'
		);
	
	
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the CampaignService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct()
    {
		parent::__construct();
		
		$this->campaignClass = new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
		$this->loadOntologies($this->deliveryOntologies);
    }
	
	/**
     * The method getCampaignClass return the current Campaign Class
	 * (not used yet in the current implementation)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getCampaignClass($uri = '')
    {
        $returnValue = null;

		if(empty($uri) && !is_null($this->campaignClass)){
			$returnValue = $this->campaignClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isCampaignClass($clazz)){
				$returnValue = $clazz;
			}
		}

        return $returnValue;
    }
		
	/**
     * Returns a campaign by providing either its uri (default) or its label and the campaign class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getCampaign($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->campaignClass;
		}
		if($this->isCampaignClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		
        return $returnValue;
    }
	
	 /**
     * Create a new subclass of Campaign, which is basically always a subclass of an existing Campaign class.
	 * Require an array('propertyName' => 'propertyValue')
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createCampaignClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->campaignClass;
		}
		
		if($this->isCampaignClass($clazz)){
		
			$campaignClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $deliveryClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' campaign property from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
			}
			$returnValue = $campaignClass;
		}

        return $returnValue;
    }
	
	/**
     * Method to be called to delete a campaign instance
     * (Method is not used in the current implementation yet)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource campaign
     * @return boolean
     */
    public function deleteCampaign( core_kernel_classes_Resource $campaign)
    {
        $returnValue = (bool) false;
		
		if(!is_null($campaign)){
			$returnValue = $campaign->delete();
		}

        return (bool) $returnValue;
    }

    /**
     * Method to be called to delete a campaign class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function deleteCampaignClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if(!is_null($clazz)){
			if($this->isCampaignClass($clazz) && $clazz->uriResource != $this->campaignClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Check whether the object is a campaign class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function isCampaignClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($clazz->uriResource == $this->campaignClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->campaignClass->getSubClasses(true) as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}

        return (bool) $returnValue;
    }
			
    /**
     * get the list of deliveries in the campaign in parameter
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource campaign
     * @return array
     */
    public function getRelatedDeliveries( core_kernel_classes_Resource $campaign){
        $returnValue = array();
		
		if(!is_null($campaign)){
		
			//get the list of deliveries, by using getSubjects(TAO_DELIVERY_CAMPAIGN_PROP,$campaign->resourceUri);
			$deliveries = core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_CAMPAIGN_PROP, $campaign->uriResource);
			foreach ($deliveries->getIterator() as $delivery){
				if($delivery instanceof core_kernel_classes_Resource ){
					$returnValue[] = $delivery->uriResource;
				}
			}
			
			// foreach($campaign->getSubClasses(false) as $subclass){
				// $returnValue = array_merge($returnValue, getRelatedDeliveries($subclass)); 
			// }
		}

        return (array) $returnValue;
    }

    /**
     * define the list of tests composing a campaign
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource campaign
     * @param  array deliveries
     * @return boolean
     */
    public function setRelatedDeliveries( core_kernel_classes_Resource $campaign, $deliveries = array())
    {
        $returnValue = (bool) false;
		
		if(!is_null($campaign)){
			//the property of the DELIVERIES that will be modified
			$campaignProp = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);
			
			//a way to remove the campaign property value of the delivery that are used to be associated to THIS campaign
			$oldRelatedDeliveries = core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_CAMPAIGN_PROP, $campaign->uriResource);
			foreach ($oldRelatedDeliveries->getIterator() as $oldRelatedDelivery) {
				//TODO check if it is a delivery instance
				
				//find a way to remove the property value associated to THIS campaign ONLY
				$remove = core_kernel_classes_ApiModelOO::singleton()->removeStatement($oldRelatedDelivery->uriResource, TAO_DELIVERY_CAMPAIGN_PROP, $campaign->uriResource, '');
				// $this->assertTrue($remove);
				
				// $oldRelatedDelivery->removePropertyValues($campaignProp);//issue with this implementation: delete all property values
			}
			
			//assign the current compaign to the selected deliveries	
			$done = 0;
			foreach($deliveries as $delivery){
				//the delivery instance to be modified
				$deliveryInstance=new core_kernel_classes_Resource($delivery);
			
				//remove the property value associated to another delivery in case ONE delivery can ONLY be associated to ONE campaign
				//if so, then change the widget from comboBox to treeView in the delivery property definition
				// $deliveryInstance->removePropertyValues($campaignProp);
				
				//now, truly assigning the campaign uri to the affected deliveries
				if($deliveryInstance->setPropertyValue($campaignProp, $campaign->uriResource)){
					$done++;
				}
			}
			if($done == count($deliveries)){
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }

} /* end of class taoDelivery_models_classes_CampaignService */

?>