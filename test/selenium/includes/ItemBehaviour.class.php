<?php
abstract class ItemBehaviour {
	
	private $selenium;
	
	public function __construct(FunctionalTestCase $selenium) {
		$this->selenium = $selenium;
	}
	
	public function getSelenium() {
		return $this->selenium;
	}
	
	public function setSelenium($selenium) {
		$this->selenium = $selenium;
	}
	
	public abstract function run();
}
?>