<?php
require_once(dirname(__FILE__) . '/QTIInteractionBehaviour.class.php');

class QTIMatchBehaviour extends QTIInteractionBehaviour {
	
	public function run() {
		// We have to check one answer for each row.
		// What is the rowCount? colCount?
		
		$selenium = $this->getSelenium();
		$xPath = $this->getXPath();
		$interactionIndex = $this->getIndex() + 1;
		$xPathRows = "/ul[@class='choice_list choice_list_rows']/li";
		$xPathCols = "/ul[@class='choice_list choice_list_cols']/li";
		
		$rowCount = $selenium->getXpathCount("${xPath}[${interactionIndex}]${xPathRows}");
    	$colCount = $selenium->getXpathCount("${xPath}[${interactionIndex}]${xPathCols}");
    	
    	// Now we need to click at index (rowX, colRandom)
    	for ($i = 0; $i < $rowCount; $i++) {
    		$randomCol = rand(0, $colCount - 1);
    		$selenium->click("//div[@id='match_node_${i}_${randomCol}']");
    	}
    	
    	// Validate
    	$this->validateItem();
	}
	
	public function getXPath() {
		return "//div[@class='qti_widget qti_match_interaction ']";
	}
}
?>