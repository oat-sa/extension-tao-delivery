<?php
require_once(dirname(__FILE__) . '/QTIInteractionBehaviour.class.php');

class QTIChoiceBehaviour extends QTIInteractionBehaviour {
	
	public function run() {
		$selenium = $this->getSelenium();
		
		// Select the correct behaviour regarding the item nature.
		$xPath = $this->getXPath();
    	$optionsCount = $selenium->getXpathCount("${xPath}/ul[@class='qti_choice_list']/li");
    	$indexToClick = rand(1, $optionsCount);
    	$selenium->click("//ul[@class='qti_choice_list']/li[${indexToClick}]");
    	$selenium->clickAndWait("//a[@id='qti_validate']");
	}
	
	public function getXPath() {
		return "//div[@class='qti_widget qti_choice_interaction  qti_simple_interaction']";
	}
}
?>