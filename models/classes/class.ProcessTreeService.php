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
 * The taoDelivery_models_classes_ProcessTreeService class allows to create the array representation of a jsTree for a given process
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
    public function __construct($currentProcess = null){
	
		$this->currentProcess = $currentProcess;
    
	}
	
	/**
     * The method creates the array representation of jstree, for a process definition 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource process
     * @return array
     */	
	public function activityTree(core_kernel_classes_Resource $process = null){
		
		$this->currentActivity = null;
		// $this->addedConnectors = array();//reinitialized for each activity loop
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
				'class' => 'node-process-root'
			),
			'children' => array()
		);
		
		//instanciate the processAuthoring service
		$processAuthoringService = new taoDelivery_models_classes_ProcessAuthoringService();
	
		$activities = array();
		$activities = $processAuthoringService->getActivitiesByProcess($process);
		// throw new Exception(var_dump($activities));
		foreach($activities as $activity){
			
			$this->currentActivity = $activity;
			$this->addedConnectors = array();//required to prevent cyclic connexion between connectors of a given activity
			
			$activityData = array();
			$activityData = $this->activityNode($activity, 'next', false);
			
			//check if it is the first activity node:
			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));//http://www.tao.lu/middleware/taoqual.rdf#119018447833116
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->uriResource == GENERIS_TRUE){
					$activityData = $this->addNodeClass($activityData, "node-activity-initial");
				}
			}
			
			//set property node:
			$activityData['children'][] = array(
				'data' => __("Property"),
				'attributes' => array(
					'id' => "prop_".tao_helpers_Uri::encode($activity->uriResource),
					'class' => 'node-property'
				)
			);
			
			//get connectors
			$connectors = $processAuthoringService->getConnectorsByActivity($activity);
			// throw new Exception("data=".var_dump($connectors));	
			/*
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
						$connectorData[] = $this->conditionNode($connectorRule);
												
						//get the "PREC"
						$prev = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
						$connectorData[] = $this->activityNode($prev, 'prec', true);
												
					}elseif(strtolower($connectorType->getLabel()) == "sequence"){
						$prev = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
						if(!taoDelivery_models_classes_ProcessAuthoringService::isActivity($prev)){
							throw new Exception("the previous activity of a sequence connector {$connector->uriResource} must be an activity {$prev->uriResource}");
						}
						$connectorData[] = $this->activityNode($prev, 'next', true);
					}
					
					//add to activity data
					$activityData['children'][] = array(
						'data' => $connectorType->getLabel().":".$connector->getLabel(),
						'attributes' => array(
							'rel' => tao_helpers_Uri::encode($connector->uriResource),
							'class' => 'node-connector-prev'
						),
						'children' => $connectorData
					);
					
					// foreach($connectorData as $data){
						// $activityData["children"][] = $data;
					// }
					
				}
			}else{
				//check if it is the initial activity, otherwise, return an undefined type;
			
				//add the default "empty" connector node: SHOULD be displayed: initial service here!!! and there should be only ONE!
				$activityData['children'][] = array(
					'data' => __('undefined'),
					'attributes' => array(
						'rel' => 'undefined',
						'class' => 'node-connector-prev'
					)
				);
			}*/
			
			//following nodes:
			if(!empty($connectors['next'])){
				//connector following the current activity: there should be only one
				foreach($connectors['next'] as $connector){
					$this->currentConnector = $connector;
					$activityData['children'][] = $this->connectorNode($connector, '', true);
				}
			}else{
				// throw new Exception("no connector associated to the activity: {$activity->uriResource}");
				//Simply not add a connector here: this should be considered as the last activity:
				$activityData = $this->addNodeClass($activityData, 'node-activity-last');			
			}
			
			//get interactive services
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
			
			//get related inference rules
			$onBeforeInferenceRuleCollection = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE));
			foreach($onBeforeInferenceRuleCollection->getIterator() as $inferenceRule){
				$activityData['children'][] = $this->inferenceRuleNode($inferenceRule, 'onBefore');
			}
			
			$onAfterInferenceRuleCollection = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONAFTERINFERENCERULE));
			foreach($onAfterInferenceRuleCollection->getIterator() as $inferenceRule){
				$activityData['children'][] = $this->inferenceRuleNode($inferenceRule, 'onAfter');
			}
			
			//add children here
			$data["children"][] = $activityData;
		}
		
		return $data;
	}
	
	protected function inferenceRuleNode(core_kernel_classes_Resource $inferenceRule, $class){
	
		if(!in_array($class, array('onBefore', 'onAfter')) || is_null($inferenceRule)){
			return array();
		}
		
		$nodeData = array(
			'data' => $inferenceRule->getLabel(),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($inferenceRule->uriResource),
				'class' => 'node-inferenceRule-'.$class
			)
		);
		
		// $if = $inferenceRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));//conditon or null
		$then = $inferenceRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_INFERENCERULES_THEN));//assignment or null only
		$else = $inferenceRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE));//assignment, inference rule or null
		
		//always show the if node:
		$nodeData['children'][]	= $this->conditionNode($inferenceRule);
		
		if(!is_null($then)){
			$thenNode = $this->assignmentNode($then);
			if(!empty($thenNode)){
				$nodeData['children'][]	= self::addNodePrefix($thenNode, 'then');
			}
		}
		if(!is_null($else)){
			$classUri = $else->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE))->uriResource;
			if($classUri == CLASS_ASSIGNMENT){
				$elseNode = $this->assignmentNode($else);
				if(!empty($thenNode)){
					$nodeData['children'][]	= self::addNodePrefix($elseNode, 'else');
				}
			}elseif($classUri == CLASS_INFERENCERULES){
				$nodeData['children'][] = self::addNodePrefix($this->inferenceRuleNode($else, $class), 'else');
			}
		}
		
		return $nodeData;
	}
	
	protected function assignmentNode(core_kernel_classes_Resource $assignment){
		$returnValue = array();
		
		if(!is_null($assignment)){
			if($assignment->getLabel() != ''){
				$returnValue = array(
					'data' => $assignment->getLabel(),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($assignment->uriResource),
						'class' => 'node-assignment'
					)
				);
			}
		}
		
		return $returnValue;
	}
	
	/**
     * The method creates the array representation a connector to fill the jsTree 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource connector
	 * @param string nodeClass
	 * @param boolean recursive
     * @return array
     */
	public function connectorNode(core_kernel_classes_Resource $connector, $nodeClass='', $recursive=false){//put the current activity as a protected property of the class Process aythoring Tree
		
		$returnValue = array();
		$connectorData = array();
					
		//type of connector:
		//if not null, get the information on the next activities. Otherwise, return an "empty" connector node, indicating that the node has just been created, i.e. at the same time as an activity
		$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), false);
		if(is_null($connectorType)){
			//create default connector node:
			$returnValue = self::addNodePrefix($this->defaultConnectorNode($connector),$nodeClass);
			return $returnValue;
		}
		
		//if it is a split type
		if( strtolower($connectorType->getLabel()) == "split"){
			//get the rule
			$connectorRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), false);
			if(!is_null($connectorRule)){
				//continue getting connector data: 
				$connectorData[] = $this->conditionNode($connectorRule);
				
				//get the "THEN"
				$then = $connectorRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), false);
				if(!is_null($then)){
					if(taoDelivery_models_classes_ProcessAuthoringService::isConnector($then)){
						$connectorActivityReference = $then->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
						if( ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($then->uriResource, $this->addedConnectors) ){
							if($recursive){
								$connectorData[] = $this->connectorNode($then, 'then', true);
								//throw new Exception("ogihfhm  ".$this->currentConnector->uriResource);//http://127.0.0.1/middleware/demo.rdf#i1266498881014202100
							}else{
								$connectorData[] = $this->activityNode($then, 'then', false);
							}
						}else{
							$connectorData[] = $this->activityNode($then, 'then', true);
						}
					}else{
						$connectorData[] = $this->activityNode($then, 'then', true);
					}
				}
				
				//same for the "ELSE"
				$else = $connectorRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE), false);
				if(!is_null($else)){
					if(taoDelivery_models_classes_ProcessAuthoringService::isConnector($else)){
						$connectorActivityReference = $else->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
						if( ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($else->uriResource, $this->addedConnectors) ){
							if($recursive){
								$connectorData[] = $this->connectorNode($else, 'else', true);
								//throw new Exception("ogihfhm  ".$this->currentConnector->uriResource);//http://127.0.0.1/middleware/demo.rdf#i1266498881014202100
							}else{
								$connectorData[] = $this->activityNode($else, 'else', false);
							}
						}else{
							$connectorData[] = $this->activityNode($else, 'else', true);
						}
					}else{
						$connectorData[] = $this->activityNode($else, 'else', true);
					}
				}
			}
		}elseif(strtolower($connectorType->getLabel()) == "sequence"){
			$next = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), false);
			
			if(!is_null($next)){
				$connectorData[] = $this->activityNode($next, 'next', true);
			}
		}else{
			throw new Exception("unknown connector type: {$connectorType->getLabel()} for connector {$connector->uriResource}");
		}
					
		//add to data
		$returnValue = array(
			'data' => $connectorType->getLabel().":".$connector->getLabel(),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($connector->uriResource),
				'class' => 'node-connector'
			)
		);
		$returnValue = self::addNodePrefix($returnValue, $nodeClass);
		
		if(!empty($connectorData)){
			$returnValue['children'] = $connectorData;
		}
		
		$this->addedConnectors[] = $connector->uriResource;
		
		return $returnValue;
	}
	
	/**
     * Add a prefix to the node data value
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param array node
	 * @param string prefix
     * @return array
     */
	public function addNodePrefix($node, $prefix=''){
		$newNode = $node;
		$labelPrefix = '';
		switch(strtolower($prefix)){
			case 'prev':
				break;
			case 'next':
				break;
			case 'if':
				$labelPrefix = __('If').' ';
				break;	
			case 'then':
				$labelPrefix = __('Then').' ';
				break;
			case 'else':
				$labelPrefix = __('Else').' ';
				break;
		}
		if(!empty($labelPrefix)){
			$newNode['data'] = $labelPrefix.$node['data'];
		}
		return $newNode;
	}
	
	/**
     * The method creates the array representation the default connector node to fill the jsTree 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource connector
	 * @param boolean prev
     * @return array
     */
	public function defaultConnectorNode(core_kernel_classes_Resource $connector, $prev = false){
		
		$returnValue = array(
			'data' => __("type??").":".$connector->getLabel()
		);
		
		if(!$prev){
			$returnValue['attributes'] = array(
				'id' => tao_helpers_Uri::encode($connector->uriResource),
				'class' => 'node-connector'
			);
		}else{
			$returnValue['attributes'] = array(
				'class' => 'node-connector-prev'
			);
		}
		
		return $returnValue;
	}
	
	/**
     * The method creates the array representation of the condition of a rule node to fill the jsTree 
     * (could be inferenceRule or transitionRule)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource rule
     * @return array
     */	
	public function conditionNode(core_kernel_classes_Resource $rule){
		
		$nodeData = array();
		
		if(!is_null($rule)){
			$data='';
			$if = $rule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			if(!is_null($if)){
				$data = $if->getLabel();
			}else{
				$data = __("(still undefined)");
			}
			$nodeData = array(
				'data' => $data,
				'attributes' => array(
					'id' => tao_helpers_Uri::encode($rule->uriResource),
					'class' => 'node-rule'
				)
			);
			
			$nodeData = self::addNodePrefix($nodeData, 'if');
		}
		
		return $nodeData;
	}

	/**
     * The method creates the array representation the default connector node to fill the jsTree 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource activity
	 * @param string nodeClass
	 * @param boolean goto
     * @return array
     */
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
				
		$nodeData = array(
			'data' => $activity->getLabel(),
			'attributes' => array(
				$linkAttribute => tao_helpers_Uri::encode($activity->uriResource),
				'class' => $class
			)
		);
		$nodeData = self::addNodePrefix($nodeData, $nodeClass);
		return $nodeData;
	}
	
	/**
     * The method adds a node class to a nodeData array
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param array nodeData
	 * @param string newClass
     * @return array
     */
	public function addNodeClass( $nodeData=array(), $newClass='' ){
		
		if(isset($nodeData['attributes']['class']) && !empty($newClass)){
			$nodeData['attributes']['class'] .= " ".$newClass;
		}
		return $nodeData;
	}
	
	//might be useless
	public function thenElseNode($connectorRule, $type){
		
		$returnValue = null;
		
		if($type=='then'){
			$property = PROPERTY_TRANSITIONRULES_THEN;
		}elseif($type=='else'){
			$property = PROPERTY_TRANSITIONRULES_ELSE;
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