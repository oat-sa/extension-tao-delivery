<?php
require_once(dirname(__FILE__) . '/QTIInteractionBehaviour.class.php');

class QTIChoiceBehaviour extends QTIInteractionBehaviour {
	
	public function run() {
		$selenium = $this->getSelenium();
		
		// Select the correct behaviour regarding the item nature.
		$xPath = $this->getXPath();
		$interactionIndex = $this->getIndex() + 1;
    	$optionsCount = $selenium->getXpathCount("${xPath}[${interactionIndex}]/ul[@class='qti_choice_list']/li");
    	$indexToClick = rand(1, $optionsCount);
    	$selenium->click("//ul[@class='qti_choice_list']/li[${indexToClick}]");
    	
    	// Valdiate
    	$this->validateItem();
	}
	
	public function getXPath() {
		return "//div[@class='qti_widget qti_choice_interaction  qti_simple_interaction']";
	}
}
?>