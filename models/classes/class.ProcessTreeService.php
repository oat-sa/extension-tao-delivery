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
 * The taoDelivery_models_classes_ProcessTreeService class p
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_ProcessTreeService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The attribute 
     *
     * @access protected
     * @var Class
     */
	protected $currentProcess = null; 
	 
    protected $currentActivity = null;

	protected $currentConnector = null;
	
	protected $addedConnectors = array();
	
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct($currentProcess = null)
    {
		$this->currentProcess = $currentProcess;
    }
	
	
	public function activityTree(core_kernel_classes_Resource $process = null){
		
		$this->currentActivity = null;
		$this->addedConnectors = array();;
		$data = array();
		
		if(empty($process) && !empty($this->currentProcess)){
			$process = $this->currentProcess;
		}
		if(empty($process)){
			throw new Exception("no process instance to populate the activity tree");
			return $data;
		}
		
		//initiate the return data value:
		$data = array(
			'data' => __("Process Activities"),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($process->uriResource),
				'class' => 'node-root'
			),
			'children' => array()
		);
		
		//instanciate the processAuthoring service
		$processAuthoringService = new taoDelivery_models_classes_ProcessAuthoringService();
	
		$activities = array();
		$activities = $processAuthoringService->getActivitiesByProcess($process->uriResource);
		
		
		foreach($activities as $activity){
			
			$this->currentActivity = $activity;
			
			$activityData = array();
			$activityData = $this->activityNode($activity, 'next', false);
			
			
			//set property node:
			$activityData['children'][] = array(
				'data' => __("Property"),
				'attributes' => array(
					'id' => "prop_".tao_helpers_Uri::encode($activity->uriResource),
					'class' => 'node-property'
				)
			);
			
			//get connectors
			// $connectors = $processAuthoringService->getConnectorsByActivity($activity->uriResource);
			
			
			if(!empty($connectors['prev'])){
			
				$this->currentConnector = null;
				
				//activity connected to a previous one:
				foreach($connectors['prev'] as $connector){
				
					$this->currentConnector = $connector;
					
					$connectorData = array();
					
					//type of connector:
					$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
					
					//if it is a split type
					if( strtolower($connectorType->getLabel()) == "split"){
						//get the rule
						$connectorRule = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
						$connectorData[] = $this->ruleNode($connectorRule);
												
						//get the "PREC"
						$prev = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
						$connectorData[] = $this->activityNode($prev, 'prec', true);
												
					}elseif(strtolower($connectorType->getLabel()) == "sequence"){
						$prev = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
						if(!taoDelivery_models_classes_ProcessAuthoringService::isActivity($prev)){
							throw new Exception("the previous activity of a sequence connector must be an activity");
						}
						$connectorData[] = $this->activityNode($prev, 'next', true);
					}
					
					//add to activity data
					$activityData[] = array(
						'data' => $connectorType->getLabel().":".$connector->getLabel(),
						'attributes' => array(
							'rel' => tao_helpers_Uri::encode($connector->uriResource),
							'class' => 'node-connector-prev'
						),
						'children' => $connectorData
					);
				}
			}
			
			//following nodes:
			if(!empty($connectors['next'])){
				//connector following the current activity: there should be only one
				foreach($connectors['next'] as $connector){
					$this->currentConnector = $connector;
					$activityData[] = $this->connectorNode($connector, true);
				}
			}
			
			//get iservices
			$services = null;
			$services = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			foreach($services->getIterator() as $service){
				if($service instanceof core_kernel_classes_Resource){
					$activityData['children'][] = array(
						'data' => $service->getLabel(),
						'attributes' => array(
							'id' => tao_helpers_Uri::encode($service->uriResource),
							'class' => 'node-interactive-service'
						)
					);
				}
			}
			
			//get related rules
			
			$data["children"][] = $activityData;
		}
		// throw new Exception("data=".var_dump($data));
		return $data;
	}
	
	public function connectorNode($connector, $recursive=false){//put the current activity as a protected property of the class Process aythoring Tree
		// $this->connectorNode($connector, true); call of function
		
		$returnValue = array();
		$connectorData = array();
					
		//type of connector:
		$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
					
		//if it is a split type
		if( strtolower($connectorType->getLabel()) == "split"){
			//get the rule
			$connectorRule = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
			$connectorData[] = $this->ruleNode($connectorRule);
			
			//get the "THEN"
			$then = $connectorRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULE_THEN));
			$connectorActivityReference = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->literal;
			if(taoDelivery_models_classes_ProcessAuthoringService::isConnector($then) && ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($then->uriResource, $this->addedConnectors)){
				if($recursive){
					$connectorData[] = $this->connectorNode($then, true);
				}else{
					$connectorData[] = $this->activityNode($then, 'then', false);
				}
			}else{
				$connectorData[] = $this->activityNode($then, 'then', true);
			}
			
			//same for the "ELSE"
			$else = $connectorRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULE_ELSE));
			$connectorActivityReference = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->literal;
			if(taoDelivery_models_classes_ProcessAuthoringService::isConnector($else) && ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($else->uriResource, $this->addedConnectors)){
				if($recursive){
					$connectorData[] = $this->connectorNode($else, true);
				}else{
					$connectorData[] = $this->activityNode($else, 'else', false);
				}
			}else{
				$connectorData[] = $this->activityNode($else, 'else', true);
			}
		}elseif(strtolower($connectorType->getLabel()) == "sequence"){
			$next = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
			$connectorData[] = $this->activityNode($next, 'next', true);
		}else{
			throw new Exception("unknown connector type");
		}
					
		//add to data
		$returnValue = array(
			'data' => $connectorType->getLabel().":".$connector->getLabel(),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($connector->uriResource),
				'class' => 'node-connector-next'
			),
			'children' => $connectorData
		);
		
		return $returnValue;
	}
						
	public function ruleNode(core_kernel_classes_Resource $rule){
		$labelPrefix = __("if").' ';
		
		$nodeData = array(
			'data' => $labelPrefix.$rule->getLabel(),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($rule->uriResource),
				'class' => 'node-rule'
			)
		);
		
		return $nodeData;
	}

	
	public function activityNode(core_kernel_classes_Resource $activity, $nodeClass='', $goto=false){
		$nodeData = array();
		$class = '';
		$linkAttribute = 'id';
		
		if(taoDelivery_models_classes_ProcessAuthoringService::isActivity($activity)){
			$class = 'node-activity';
		}elseif(taoDelivery_models_classes_ProcessAuthoringService::isConnector($activity)){
			$class = 'node-connector';
		}else{
			return $nodeData;//unknown type
		}
		
		if($goto){
			$class .= "-goto";
			$linkAttribute = "rel";
		}
		
		$labelPrefix = '';
		switch(strtolower($nodeClass)){
			case 'prev':
				break;
			case 'next':
				break;
			case 'then':
				$labelPrefix = __('then').' ';
				break;
			case 'else':
				$labelPrefix = __('else').' ';
				break;
		}
		
		$nodeData = array(
			'data' => $labelPrefix.$activity->getLabel(),
			'attributes' => array(
				$linkAttribute => tao_helpers_Uri::encode($activity->uriResource),
				'class' => $class
			)
		);
		
		return $nodeData;
	}
	
	public function thenElseNode($connectorRule, $type){
		
		$returnValue = null;
		
		if($type=='then'){
			$property = PROPERTY_TRANSITIONRULE_THEN;
		}elseif($type=='else'){
			$property = PROPERTY_TRANSITIONRULE_ELSE;
		}else{
			throw new Exception('choose either "then" or "else"');
			return $returnValue ;
		}
		
		$nextActivity = $connectorRule->getUniquePropertyValue(new core_kernel_classes_Property($property));
		$connectorActivityReference = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->literal;
		if(taoDelivery_models_classes_ProcessAuthoringService::isConnector($nextActivity) && ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($nextActivity->uriResource, $this->addedConnectors)){
			if($recursive=true){//TODO: keep it or not?
				$returnValue = $this->connectorNode($nextActivity, true);
			}else{
				$returnValue = $this->activityNode($nextActivity, $type, false);
			}
		}else{
			$returnValue = $this->activityNode($nextActivity, $type, true);
		}
		
		return $returnValue;
	}
} /* end of class  */

?>