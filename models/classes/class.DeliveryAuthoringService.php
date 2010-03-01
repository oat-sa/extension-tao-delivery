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

require_once('taoDelivery/helpers/class.Precompilator.php');

/**
 * The taoDelivery_models_classes_ProcessAuthoringService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_DeliveryAuthoringService
    extends taoDelivery_models_classes_ProcessAuthoringService
{
   

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
		
    }
		
	//use in delivery compilator
	public function getTestByActivity(core_kernel_classes_Resource $activity){
		$returnValue = null;
		
		if(!empty($activity)){
			
			foreach ($activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->getIterator() as $iService){
				if($iService instanceof core_kernel_classes_Resource){
						
					$serviceDefinition = $iService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
					if(!is_null($serviceDefinition)){
					
						$serviceUrl = $serviceDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL));
						//NOTE: PROPERTY_SUPPORTSERVICES_URL only valid for support service and not for web services
						if(!is_null($serviceUrl) && $serviceUrl instanceof core_kernel_classes_Resource){//the problem is that an url is interpreted as a uri so it the getOnePropertyValue return it as a resource
							//check if the url is a compiled test one:
							
							$testUri = tao_helpers_Precompilator::getTestUri($serviceUrl->uriResource);
							
								// throw new Exception("sdgdsg==".var_dump($serviceUrl)."&$testUri");
							
							
							if(!empty($testUri)){
								$returnValue = new core_kernel_classes_Resource($testUri);
							}
						}
						
					}
						
				}
				
			}
			
		}
		
		return $returnValue;
	}
	
	public function getDeliveryFromProcess(core_kernel_classes_Resource $process){
		
		$delivery = null;
		
		$deliveryCollection = core_kernel_classes_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_DELIVERYCONTENT,$process->uriResource);
		if(!$deliveryCollection->isEmpty()){
			$delivery = $deliveryCollection->get(0);
		}
		
		return $delivery;
	}
		

} /* end of class taoDelivery_models_classes_DeliveryService */

?>