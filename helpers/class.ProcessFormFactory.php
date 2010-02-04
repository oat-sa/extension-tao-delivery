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
	public static function callOfServiceEditor(core_kernel_classes_Resource $callOfService, core_kernel_classes_Resource $serviceDefinition = null){
		
		$returnValue = "";
		$returnValue = "<ul>";
		
		//check if the property value serviceDefiniiton PROPERTY_CALLOFSERVICES_SERVICEDEFINITION of the current callOfService exists
		if(empty($serviceDefinition)){
			
			$collection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
			if($collection->count()<=0){
				//if the serviceDefinition is not set yet, simply return a dropdown menu of available servicedefinition
				$returnValue .= "<li>dropdown</li><ul>";
				return $returnValue;
			}
			else{
				foreach ($collection->getIterator() as $value){
					if($value instanceof core_kernel_classes_Resource){
						$serviceDefinition = $value;
						break;
						//a service definition has been found
					}
				}
				if(empty($serviceDefinition)){
					//if still empty:
					throw new Exception("no service definition has been found for the call of service that is being edited");
					return $returnValue;
				}
			}
		}
		
		//else continue to build the form associated to the selected service:
		//get list of parameters from the service definition PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT and IN
		$returnValue .= self::getCallOfServiceFormElements($serviceDefinition, $callOfService, "formalParameterIn");
		$returnValue .= self::getCallOfServiceFormElements($serviceDefinition, $callOfService, "formalParameterOut");
		
		
		//for each parameter, get the default value if exists:
		//PROPERTY_SERVICESDEFINITION_FORMALPARAM -> PROPERTY_CALLOFSERVICES_ACTUALPARAM
				
		//create a form element and fill the content with the default value
		$returnValue .= "</ul>";		
        return $returnValue;
	}
	
	private static function getCallOfServiceFormElements(core_kernel_classes_Resource $serviceDefinition, core_kernel_classes_Resource $callOfService, $paramType){
	
		$returnValue = "";//array();
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
		if(strtolower($paramType) == "formalparameterin"){
			$formalParameterType = PROPERTY_SERVICESDEFINITION_FORMALPARAMIN;
			$formalParameterInOutType = PROPERTY_CALLOFSERVICES_ACTUALPARAMIN;
		}elseif(strtolower($paramType) == "formalparameterout"){
			$formalParameterType = PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT;
			$formalParameterInOutType = PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT;
		}else{
			throw new Exception("unsupported formalParameter type : $paramType");
		}
		
		//start creating the BLOC of form element
		$returnValue .= "<li>$paramType:</li>";
		
		$collection = $serviceDefinition->getPropertyValuesCollection(new core_kernel_classes_Property($formalParameterType));
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
				$actualParamFromCallOfServices = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property($formalParameterInOutType)); 
				
				//make an intersect with $collection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT));
				$actualParam = $actualParamFromFormalParam->intersect($actualParamFromCallOfServices);
				if($actualParam->count()>0){
					if($actualParam->get(0) instanceof core_kernel_classes_Resource){
						//the actual param associated to the formal parameter of THE call of services has been found!
						
						//to be clarified(which one to use, how and when???):
						$actualParameterType = PROPERTY_ACTUALPARAM_PROCESSVARIABLE; //PROPERTY_ACTUALPARAM_CONSTANTVALUE;//PROPERTY_ACTUALPARAM_PROCESSVARIABLE //PROPERTY_ACTUALPARAM_QUALITYMETRIC
						
						$actualParamValueCollection = $actualParam->get(0)->getPropertyValuesCollection(new core_kernel_classes_Property($actualParameterType));
						if($actualParamValueCollection->count() > 0){
							if($actualParamValueCollection->get(0) instanceof core_kernel_classes_Resource){
								$actualParamValue = $actualParamValueCollection->get(0)->uriResource;
							}elseif($actualParamValueCollection->get(0) instanceof core_kernel_classes_Literal){
								$actualParamValue = $actualParamValueCollection->get(0)->literal;
							}
							$inputValue = $actualParamValue;
						}
						
					}
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
				$returnValue .= "<li>name:$inputName uri:$inputUri value:$inputValue</li>";
			}
		}
		
		return $returnValue;
	}
    
} /* end of class taoDelivery_helpers_ProcessFormFactory */

?>