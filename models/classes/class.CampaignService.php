<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery\models\classes\class.CampaignService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.12.2010, 16:21:02 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201B-includes begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201B-includes end

/* user defined constants */
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201B-constants begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201B-constants end

/**
 * Short description of class taoDelivery_models_classes_CampaignService
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_CampaignService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute campaignClass
     *
     * @access protected
     * @var Class
     */
    protected $campaignClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002049 begin
		parent::__construct();
		$this->campaignClass = new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002049 end
    }

    /**
     * Short description of method createCampaignClass
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createCampaignClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000204B begin
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
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000204B end

        return $returnValue;
    }

    /**
     * Short description of method deleteCampaign
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource campaign
     * @return boolean
     */
    public function deleteCampaign( core_kernel_classes_Resource $campaign)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000204D begin
		if(!is_null($campaign)){
			$returnValue = $campaign->delete();
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000204D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteCampaignClass
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteCampaignClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000204F begin
		if(!is_null($clazz)){
			if($this->isCampaignClass($clazz) && $clazz->uriResource != $this->campaignClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000204F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCampaign
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Integer identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getCampaign( tao_helpers_form_validators_Integer $identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002051 begin
		if(is_null($clazz)){
			$clazz = $this->campaignClass;
		}
		if($this->isCampaignClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002051 end

        return $returnValue;
    }

    /**
     * Short description of method getCampaignClass
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getCampaignClass($uri = '')
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002057 begin
		if(empty($uri) && !is_null($this->campaignClass)){
			$returnValue = $this->campaignClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isCampaignClass($clazz)){
				$returnValue = $clazz;
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002057 end

        return $returnValue;
    }

    /**
     * Short description of method getRelatedDeliveries
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource campaign
     * @return array
     */
    public function getRelatedDeliveries( core_kernel_classes_Resource $campaign)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002059 begin
		if(!is_null($campaign)){
		
			$campaignClass = new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
			$deliveries = $campaignClass->searchInstances(array(TAO_DELIVERY_CAMPAIGN_PROP => $campaign->uriResource), array('like'=>false, 'recursive' => true));
			foreach ($deliveries as $delivery){
				if($delivery instanceof core_kernel_classes_Resource ){
					$returnValue[] = $delivery->uriResource;
				}
			}
		}
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002059 end

        return (array) $returnValue;
    }

    /**
     * Short description of method isCampaignClass
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Class clazz
     * @return boolean
     */
    public function isCampaignClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000205B begin
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
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000205B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setRelatedDeliveries
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource campaign
     * @param  array deliveries
     * @return boolean
     */
    public function setRelatedDeliveries( core_kernel_classes_Resource $campaign, $deliveries = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000205D begin
		if(!is_null($campaign)){
			//the property of the DELIVERIES that will be modified
			$campaignProp = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);
			
			//a way to remove the campaign property value of the delivery that are used to be associated to THIS campaign
			$deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
			$oldDeliveries = $deliveryClass->searchInstances(array(TAO_DELIVERY_CAMPAIGN_PROP => $campaign->uriResource), array('like'=>false, 'recursive' => true));
			foreach ($oldDeliveries as $oldRelatedDelivery) {
				//TODO check if it is a delivery instance
				
				//find a way to remove the property value associated to THIS campaign ONLY
				$remove = core_kernel_impl_ApiModelOO::singleton()->removeStatement($oldRelatedDelivery->uriResource, TAO_DELIVERY_CAMPAIGN_PROP, $campaign->uriResource, '');
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
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000205D end

        return (bool) $returnValue;
    }

} /* end of class taoDelivery_models_classes_CampaignService */

?>