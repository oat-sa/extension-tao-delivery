<?php

error_reporting(E_ALL);

/**
 * The GenerisFormFactory enables you to create Forms using rdf data and the
 * api to provide it. You can give any node of your ontology and the factory
 * create the appriopriate form. The Generis ontology (with the Widget Property)
 * required to use it.
 * Now only the xhtml rendering mode is implemented
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @see core_kernel_classes_* packages
 * @subpackage helpers_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The GenerisFormFactory enables you to create Forms using rdf data and the
 * api to provide it. You can give any node of your ontology and the factory
 * create the appriopriate form. The Generis ontology (with the Widget Property)
 * required to use it.
 * Now only the xhtml rendering mode is implemented
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @see core_kernel_classes_* packages
 * @subpackage helpers_form
 */
class taoDelivery_helpers_ProcessFormFactory extends tao_helpers_form_GenerisFormFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * the default top level (to stop the recursivity look up) class commonly used
     *
     * @access public
     * @var string
     */
    const DEFAULT_TOP_LEVEL_CLASS = 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource';
	
	/**
     * Create a form from a class of your ontology, the form data comes from the
     * The default rendering is in xhtml
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  Resource instance
     * @param  string name
     * @param  array options
     * @return tao_helpers_form_Form
     */
    public static function instanceEditor( core_kernel_classes_Class $clazz,  core_kernel_classes_Resource $instance = null, $name = '', $options = array(), $excludedProp = array())
    {
        $returnValue = null;

		if(!is_null($clazz)){
			
			if(empty($name)){
				$name = 'form_'.(count(self::$forms)+1);
			}
			
			$myForm = tao_helpers_form_FormFactory::getForm($name, $options);
			
			$level = 2;
					
			$defaultProperties 	= self::getDefaultProperties();
			
			$classProperties = self::getClassProperties($clazz, new core_kernel_classes_Class(self::DEFAULT_TOP_LEVEL_CLASS));
					
			$maxLevel = count(array_merge($defaultProperties, $classProperties));
			foreach(array_merge($defaultProperties, $classProperties) as $property){
				
				if(!empty($excludedProp) && in_array($property->uriResource, $excludedProp)){
					continue;
				}
				
				$property->feed();
				
				//map properties widgets to form elments 
				$element = self::elementMap($property);
				
				if(!is_null($element)){
			
					//take instance values to populate the form
					if(!is_null($instance)){
						$values = $instance->getPropertyValuesCollection($property);
						foreach($values->getIterator() as $value){
							if(!is_null($value)){
								if($value instanceof core_kernel_classes_Resource){
									$element->setValue($value->uriResource);
								}
								if($value instanceof core_kernel_classes_Literal){
									$element->setValue((string)$value);
								}
							}
						}
					}
					if(in_array($property, $defaultProperties)){
						$element->setLevel($level);
						$level++;
					}
					else{
						$element->setLevel($maxLevel + $level);
						$maxLevel++;
					}
					$myForm->addElement($element);
				}
			}
			
			//add an hidden elt for the class uri
			$classUriElt = tao_helpers_form_FormFactory::getElement('classUri', 'Hidden');
			$classUriElt->setValue(tao_helpers_Uri::encode($clazz->uriResource));
			$classUriElt->setLevel($level);
			$myForm->addElement($classUriElt);
			
			if(!is_null($instance)){
				//add an hidden elt for the instance Uri
				$instanceUriElt = tao_helpers_form_FormFactory::getElement('uri', 'Hidden');
				$instanceUriElt->setValue(tao_helpers_Uri::encode($instance->uriResource));
				$instanceUriElt->setLevel($level+1);
				$myForm->addElement($instanceUriElt);
			}
			
			//form data evaluation
			$myForm->evaluate();
				
			self::$forms[$name] = $myForm;
			$returnValue = self::$forms[$name];
		}
		
        return $returnValue;
    }
	
	//$callOfService already created beforehand in the model
	public static function callOfServiceEditor(core_kernel_classes_Resource $callOfService, core_kernel_classes_Resource $serviceDefinition = null, $formName=''){
		
		if(empty($formName)){
			$formName = 'callOfServiceEditor';
		}
		$myForm = null;
		$myForm = tao_helpers_form_FormFactory::getForm($formName, array());
		$myForm->setActions(array(), 'bottom');//delete the default 'save' and 'revert' buttons
		
		//add a hidden input to post the uri of the call of service that is being edited
		$classUriElt = tao_helpers_form_FormFactory::getElement('callOfServiceUri', 'Hidden');
		$classUriElt->setValue(tao_helpers_Uri::encode($callOfService->uriResource));
		// $classUriElt->setLevel($level);
		$myForm->addElement($classUriElt);
		
		//add label input:
		$elementLabel = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
		$elementLabel->setDescription(__('Label'));
		$elementLabel->setValue($callOfService->getLabel());
		$myForm->addElement($elementLabel);
		
		//add a drop down select input to allow selecting ServiceDefinition
		$elementServiceDefinition = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), 'Combobox');
		$elementServiceDefinition->setDescription(__('Service Definition'));
		$range = new core_kernel_classes_Class(CLASS_SERVICESDEFINITION);
		if($range != null){
			$options = array();
			foreach($range->getInstances(true) as $rangeInstance){
				$options[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
			}
			$elementServiceDefinition->setOptions($options);
		}
		// $myForm->addElement($elementServiceDefinition);
		
		//check if the property value serviceDefiniiton PROPERTY_CALLOFSERVICES_SERVICEDEFINITION of the current callOfService exists
		if(empty($serviceDefinition)){
			
			//get list of available service definition
			$collection = null;
			$collection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
			if($collection->count()<=0){
				//if the serviceDefinition is not set yet, simply return a dropdown menu of available servicedefinition
				$myForm->addElement($elementServiceDefinition);
				return $myForm;
			}
			else{
				foreach ($collection->getIterator() as $value){
					if($value instanceof core_kernel_classes_Resource){//a service definition has been found!
						$serviceDefinition = $value;
						$elementServiceDefinition->setValue($serviceDefinition->uriResource);//no need to use tao_helpers_Uri::encode here: seems like that it would be done sw else
						$myForm->addElement($elementServiceDefinition);
						break;//stop at the first occurence, which should be the unique one
					}
				}
			}
		}
		
		//if the service definition is still not set here,there is a problem
		if(empty($serviceDefinition)){
			throw new Exception("an empty value of service definition has been found for the call of service that is being edited");
			return $myForm;
		}
		//useless because already in the select field
		/*else{
			//add a hidden input element to allow easier form value evaluation after submit
			$serviceDefinitionUriElt = tao_helpers_form_FormFactory::getElement('serviceDefinitionUri', 'Hidden');
			$serviceDefinitionUriElt->setValue(tao_helpers_Uri::encode($serviceDefinition->uriResource));
			// $classUriElt->setLevel($level);
			$myForm->addElement($serviceDefinitionUriElt);
		}*/
		
		//continue building the form associated to the selected service:
		//get list of parameters from the service definition PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT and IN
		//create a form element and fill the content with the default value
		$elementInputs = array_merge(
			self::getCallOfServiceFormElements($serviceDefinition, $callOfService, "formalParameterIn"),
			self::getCallOfServiceFormElements($serviceDefinition, $callOfService, "formalParameterOut")
		);
		
		$handle = fopen("/elementInputs.txt","wb");
			$fileContent = fwrite($handle,json_encode($elementInputs));
			fclose($handle);
		
		// $elementInputs = self::getCallOfServiceFormElements($serviceDefinition, $callOfService, "formalParameterin");
		foreach($elementInputs as $elementInput){
			$myForm->addElement($elementInput);
		}
		
        return $myForm;
	}
	
	//return an array of elments
	protected static function getCallOfServiceFormElements(core_kernel_classes_Resource $serviceDefinition, core_kernel_classes_Resource $callOfService, $paramType){
	
		$returnValue = array();//array();
		if(empty($paramType) || empty($serviceDefinition)){
			return $returnValue;
		}
		
		if(!($serviceDefinition instanceof core_kernel_classes_Resource)){
			throw new Exception('serviceDefinition must be a resource');
			return $returnValue;
		}
		if(!($callOfService instanceof core_kernel_classes_Resource)){
			throw new Exception('callOfService must be a resource');
			return $returnValue;
		}
		
		$formalParameterType = '';
		$actualParameterInOutType = '';
		$formalParameterName = '';
		$formalParameterSuffix = '';
		if(strtolower($paramType) == "formalparameterin"){
		
			$formalParameterType = PROPERTY_SERVICESDEFINITION_FORMALPARAMIN;
			$actualParameterInOutType = PROPERTY_CALLOFSERVICES_ACTUALPARAMIN;
			$formalParameterName = __('Formal Parameter IN'); 
			$formalParameterSuffix = '_IN';
			
		}elseif(strtolower($paramType) == "formalparameterout"){
		
			$formalParameterType = PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT;
			$actualParameterInOutType = PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT;
			$formalParameterName = __('Formal Parameter OUT');
			$formalParameterSuffix = '_OUT';
			
		}else{
			throw new Exception("unsupported formalParameter type : $paramType");
		}
		
		//get the other parameter input elements
		$collection = null;
		$collection = $serviceDefinition->getPropertyValuesCollection(new core_kernel_classes_Property($formalParameterType));
		if($collection->count()>0){
			//start creating the BLOC of form element
			$descriptionElement = tao_helpers_form_FormFactory::getElement($paramType, 'Free');
			$descriptionElement->setValue($formalParameterName.' :');
			$returnValue[$paramType]=$descriptionElement;
		}
		
		foreach ($collection->getIterator() as $formalParam){
			if($formalParam instanceof core_kernel_classes_Resource){
			
				//create a form element:
				$inputName = $formalParam->getLabel();//which will be equal to $actualParam->getLabel();
				$inputUri = $formalParam->uriResource;
				// $inputUri = "";
				$inputValue = "";
				
				
				//get current value:PROPERTY_ACTUALPARAM_CONSTANTVALUE
				//find actual param first!
				$actualParamValue='';
				$actualParamFromFormalParam = core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_ACTUALPARAM_FORMALPARAM, $formalParam->uriResource);
				$actualParamFromCallOfServices = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property($actualParameterInOutType)); 
				
				//make an intersect with $collection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT));
				$actualParamCollection = $actualParamFromFormalParam->intersect($actualParamFromCallOfServices);
				// throw new Exception("vardump=". var_dump($actualParam));//debug
				if(!$actualParamCollection->isEmpty()){
					foreach($actualParamCollection->getIterator() as $actualParam){
						if($actualParam instanceof core_kernel_classes_Resource){
							//the actual param associated to the formal parameter of THE call of services has been found!
						
							//to be clarified(which one to use, how and when???):
							$actualParameterType = PROPERTY_ACTUALPARAM_PROCESSVARIABLE; //PROPERTY_ACTUALPARAM_CONSTANTVALUE;//PROPERTY_ACTUALPARAM_PROCESSVARIABLE //PROPERTY_ACTUALPARAM_QUALITYMETRIC
							
							$actualParamValueCollection = $actualParam->getPropertyValuesCollection(new core_kernel_classes_Property($actualParameterType));
							if(!$actualParamValueCollection->isEmpty()){
								if($actualParamValueCollection->get(0) instanceof core_kernel_classes_Resource){
									$actualParamValue = $actualParamValueCollection->get(0)->uriResource;
								}elseif($actualParamValueCollection->get(0) instanceof core_kernel_classes_Literal){
									$actualParamValue = $actualParamValueCollection->get(0)->literal;
								}
								$inputValue = $actualParamValue;
							}
						}
					}
					/*
					if($actualParam->get(0) instanceof core_kernel_classes_Resource){
						//the actual param associated to the formal parameter of THE call of services has been found!
						
						//to be clarified(which one to use, how and when???):
						$actualParameterType = PROPERTY_ACTUALPARAM_PROCESSVARIABLE; //PROPERTY_ACTUALPARAM_CONSTANTVALUE;//PROPERTY_ACTUALPARAM_PROCESSVARIABLE //PROPERTY_ACTUALPARAM_QUALITYMETRIC
						
						// $actualParamValueCollection = $actualParam->get(0)->getPropertyValuesCollection(new core_kernel_classes_Property($actualParameterType));
						// if($actualParamValueCollection->count() > 0){
							// if($actualParamValueCollection->get(0) instanceof core_kernel_classes_Resource){
								// $actualParamValue = $actualParamValueCollection->get(0)->uriResource;
							// }elseif($actualParamValueCollection->get(0) instanceof core_kernel_classes_Literal){
								// $actualParamValue = $actualParamValueCollection->get(0)->literal;
							// }
							// $inputValue = $actualParamValue;
						// }
					}*/
				}
				
				// if($actualParam->count()>0){
					// if($actualParam->get(0) instanceof core_kernel_classes_Resource){
						// $actualParamValueCollection = $actualParam->get(0)->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_CONSTANTVALUE));
						// if($actualParamValueCollection->count() > 0){
							// $actualParamValue = $actualParamValueCollection->get(0)->literal;
							// $inputValue = $actualParamValue;
						// }
					// }
				// }
				
				/*
				if(empty($inputUri)){//place ce bloc dans la creation de call of service: cad retrouver systematiquement l'actual parameter associé à chaque fois, à partir du formal parameter et call of service, lors de la sauvegarde
					// if no actual parameter has been found above (since $inputUri==0) create an instance of actual parameter and associate it to the call of service:
					$property_actualParam_formalParam = new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_FORMALPARAM);
					$class_actualParam = new core_kernel_classes_Class(CLASS_ACTUALPARAM);
					$newActualParameter = $class_actualParam->createInstance($formalParam->getLabel(), "created by ProcessFormFactory");
					$newActualParameter->setPropertyValue($property_actualParam_formalParam, $formalParam->uriResource);
					
					// $inputUri = $newActualParameter->uriResource;//we add an "empty" value in 
				}
				*/
				
				if(empty($inputValue)){
					//if no value set yet, try finding the default value:
					$defaultValue = "";
					$paramValueCollection = $formalParam->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_FORMALPARAM_DEFAULTVALUE));
					if($paramValueCollection->count()>0){
						if($paramValueCollection->get(0) instanceof core_kernel_classes_Literal){
							$defaultValue = $paramValueCollection->get(0)->literal;
							$inputValue = $defaultValue;
						}
					}
				}
				
				//create the form element here:
				$element = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode($inputUri).$formalParameterSuffix, 'Textbox');
				$element->setDescription($inputName);
				$element->setValue($inputValue);
				
				$returnValue[tao_helpers_Uri::encode($inputUri).$formalParameterSuffix] = $element;
			}
		}
		
		return $returnValue;
	}
	
	public function connectorEditor(core_kernel_classes_Resource $connector, core_kernel_classes_Resource $connectorType=null){
		if(empty($formName)){
			$formName = 'connectorForm';
		}
		$myForm = null;
		$myForm = tao_helpers_form_FormFactory::getForm($formName, array());
		$myForm->setActions(array(), 'bottom');//delete the default 'save' and 'revert' buttons
		
		//add a hidden input to post the uri of the call of service that is being edited
		$elementConnectorUri = tao_helpers_form_FormFactory::getElement('connectorUri', 'Hidden');
		$elementConnectorUri->setValue(tao_helpers_Uri::encode($connector->uriResource));
		// $classUriElt->setLevel($level);
		$myForm->addElement($elementConnectorUri);
		
		//add label input: authorize connector label editing or not?
		// $elementLabel = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
		// $elementLabel->setDescription(__('Label'));
		// $elementLabel->setValue($callOfService->getLabel());
		// $myForm->addElement($elementLabel);
		
		//add a drop down select input to allow selecting Type of Connector
		$elementConnectorType = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE), 'Combobox');
		$elementConnectorType->setDescription(__('Connector Type'));
		$range = new core_kernel_classes_Class(CLASS_TYPEOFCONNNECTORS);
		if($range != null){
			$options = array();
			foreach($range->getInstances(true) as $rangeInstance){
				$options[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
			}
			$elementConnectorType->setOptions($options);
		}
		
		//check if the property value "type of connector" of the current connector exists
		if(empty($connectorType)){
			
			//get list of available types of connector
			$collection = null;
			$collection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if($collection->count()<=0){
				//if the type of connector is not set yet, simply return a dropdown menu of available type of connector
				$myForm->addElement($elementConnectorType);
				return $myForm;
			}
			else{
				foreach ($collection->getIterator() as $value){
					if($value instanceof core_kernel_classes_Resource){//a connector type has been found!
						$connectorType = $value;
						$elementConnectorType->setValue($connectorType->uriResource);//no need to use tao_helpers_Uri::encode here: seems like that it would be done sw else
						$myForm->addElement($elementConnectorType);
						break;//stop at the first occurence, which should be the unique one
					}
				}
			}
		}
		
		//if the type of connector is still not set here,there is a problem
		if(empty($connectorType)){
			throw new Exception("an empty value of service definition has been found for the call of service that is being edited");
			return $myForm;
		}
		
		//continue building the form according the the type of connector:
		$elementInputs=array();
		if($connectorType->uriResource == TYPEOFCONNECTORS_SEQUENCE){
			$elementInputs = $this->nextActivityEditor($connector, $type);//next, then, else
		}else if($connectorType->uriResource == TYPEOFCONNECTORS_SPLIT){
		
		}else{
			throw new Exception("the selected type of connector {$connectorType->getLabel()} is not supported yet")
		}
		
		foreach($elementInputs as $elementInput){
			$myForm->addElement($elementInput);
		}
		
        return $myForm;
	}
	
	public function nextActivityEditor(core_kernel_classes_Resource $connector, $type){
		$returnValue = array();
		
		$nextActivity = null;
		//find the next activity if available
		switch(strtolower($type)){
			case 'next':
				$nextActivityCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
				foreach($nextActivityCollection->getIterator() as $activity){
					if($activity instanceof core_kernel_classes_Resource){
						$nextActivity = $activity;//we take the last one...(note: there should be only one though)
					}
				}
				break;
			case 'then':
				$transitionRuleCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
				foreach($nextActivityCollection->getIterator() as $transitionRule){
					if($transitionRule instanceof core_kernel_classes_Resource){
						foreach($transitionRule->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->getIterator() as $then){
							if($then instanceof core_kernel_classes_Resource){
								$nextActivity = $then;
							}
						};
					}
				}
				break;
			case 'else':
				$transitionRuleCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
				foreach($nextActivityCollection->getIterator() as $transitionRule){
					if($transitionRule instanceof core_kernel_classes_Resource){
						foreach($transitionRule->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->getIterator() as $else){
							if($else instanceof core_kernel_classes_Resource){
								$nextActivity = $else;
							}
						};
					}
				}
				break;
			default:
				throw new Exception("unknown type for the next activity");
		}
		
		//create the description element
		$descriptionElement = tao_helpers_form_FormFactory::getElement($type, 'Free');
		$descriptionElement->setValue(strtoupper($type).' :');
		$returnValue[$type] = $descriptionElement;
		
		//the default radio button to select between the 3 possibilities:
		$elementChoice = tao_helpers_form_FormFactory::getElement("activityOrConnector", 'Radiobox');
		$elementChoice->setDescription(__('Activity or Connector'));
		$options = array(
			"unknown" => __("Edit Later"),
			"activity" => __("Activity"),
			"connector" => __("Connector")
		);
		$elementChoice->setOptions($options);
		
		//TODO: decide to directly use the property uri in the form or not?
		$elementActivities = tao_helpers_form_FormFactory::getElement("activityUri", 'Combobox');
		$elementActivities->setDescription(__('Activity'));
		//require the authoring service model here to get: 1- the process associated to the connector(connector->activity->process) and 2-the list of activities of the process
		
		$range = new core_kernel_classes_Class(CLASS_SERVICESDEFINITION);
		if($range != null){
			$options = array();
			$options["newActivity"]=__("create new activity");
			foreach($range->getInstances(true) as $rangeInstance){
				$options[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
			}
			$elementActivities->setOptions($options);
		}
		
		
		if(empty($nextActivity)){
			//set the default form
			
		}else{
			//is it an activity or another connector?
			$activityType = core_kernel_classes_ApiModelOO::singleton()->getObject($prev->uriResource, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
			if( $activityType == CLASS_ACTIVITIES){
				$elementChoice->setValue("activity");
			}elseif($activityType == CLASS_CONNECTORS){
				$elementChoice->setValue("connector");
			}
			
		}
		
	}
    
} /* end of class taoDelivery_helpers_ProcessFormFactory */

?>