<?php
require_once(dirname(__FILE__) . '/QTIBehaviour.class.php');

abstract class QTIInteractionBehaviour extends QTIBehaviour {
	
	private $index;
	
	public function __construct(FunctionalTestCase $selenium, $index) {
		$this->setSelenium($selenium);
		$this->index = $index;
	}
	
	public function getIndex() {
		return $index;
	}
	
	public function setIndex($index) {
		$this->index = $index;
	}
	
	public abstract function getXPath();
}
?>