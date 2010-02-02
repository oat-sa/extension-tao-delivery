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
	
	public static function callOfServiceEditor(core_kernel_classes_Resource $callOfService, $serviceDefinition = null){
		
		$returnValue = null;
		$serviceDefinition = null;

		//check if the property value serviceDefiniiton PROPERTY_CALLOFSERVICES_SERVICEDEFINITION of the current callOfService exists
		if(!is_set($serviceDefinition)){
			$collection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
			if($collection->count()<=0){
				//if exists, return simply a dropdown menu of available servicedefinition
				return $returnValue;
			}
			
			foreach ($collection->getIterator() as $value){
				if($value instanceof core_kernel_classes_Resource ){
					$serviceDefinition = $value;
				}
				// if ( $value instanceof core_kernel_classes_Literal){
					// $this->assertEqual($value->literal, 'plop');
				// }
			}
		}
		
		//else continue to build the form associated to the selected service:
		//get list of parameters from the service definition PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT and IN
		$collection = $serviceDefinition->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT));
		foreach ($collection->getIterator() as $formalParamOut){
			if($formalParamOut instanceof core_kernel_classes_Resource){
			//create a form element:
				$formalParamOut->getLabel();
				$elementValue = "";
				
				//get current value:PROPERTY_ACTUALPARAM_CONSTANTVALUE
				//find actual param first!
				$actualParamValue='';
				$actualParam = core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_ACTUALPARAM_FORMALPARAM, $formalParamOut->uriResource);
				if($actualParam->count()>0){
					if($actualParam->get(0) instanceof core_kernel_classes_Resource){
						$actualParamValueCollection = $actualParam->get(0)->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_CONSTANTVALUE));
						if($actualParamValueCollection->count() > 0){
							$actualParamValue = $actualParamValueCollection->get(0)->Literal;
							$elementValue = $actualParamValue;
						}
					}
				}
				
				if(empty($actualParamValue)){
					//if no value set yet, getdefault value:
					$defaultValue = "";
					$paramValueCollection = $formalParamOut->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_FORMALPARAM_DEFAULTVALUE));
					if($paramValueCollection->count()>0){
						if($paramValueCollection->get(0) instanceof core_kernel_classes_Literal){
							$defaultValue = $paramValueCollection->get(0)->literal;
							$elementValue = $defaultValue;
						}
					}
				}
			}
		}
		getElement($name = '', $type = '');
		PROPERTY_ACTUALPARAM_CONSTANTVALUE
		PROPERTY_ACTUALPARAM_FORMALPARAM PROPERTY_FORMALPARAM
		//for each parameter, get the default value if exists:
		//PROPERTY_SERVICESDEFINITION_FORMALPARAM -> PROPERTY_CALLOFSERVICES_ACTUALPARAM
		$defaultValue="";
		
		//create a form element and fill the content with the default value
		
		//default
		
		$clazz = core_kernel_classes_Class();
		
		if(!is_null($clazz)){
			
			if(empty($name)){
				$name = 'form_'.(count(self::$forms)+1);
			}
			
			$myForm = tao_helpers_form_FormFactory::getForm($name, array());
			
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
    
} /* end of class taoDelivery_helpers_ProcessFormFactory */

?>