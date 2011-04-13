<?php
require_once(dirname(__FILE__) . '/QTIBehaviour.class.php');

abstract class QTIInteractionBehaviour extends QTIBehaviour {
	
	private $index;
	
	public function __construct(FunctionalTestCase $selenium, $index) {
		$this->setSelenium($selenium);
		$this->index = $index;
	}
	
	public function getIndex() {
		return $this->index;
	}
	
	public function setIndex($index) {
		$this->index = $index;
	}
	
	public function validateItem() {
		$selenium = $this->getSelenium();
		$selenium->clickAndWait("//a[@id='qti_validate']");
	}
	
	public abstract function getXPath();
}
?>