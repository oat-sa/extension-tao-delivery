<?php
require_once(dirname(__FILE__) . '/../../../tao/test/selenium/includes/FunctionalTestCase.class.php');
require_once(dirname(__FILE__) . '/includes/BehaviourFramework.php');
require_once(dirname(__FILE__) . '/../../includes/raw_start.php');

class SeleniumFrontendDeliveryTestCase extends FunctionalTestCase {

	public function setUp()
    {
		parent::setUp();
    }
 
    public function testDelivery()
    {
    	$this->openLocation('frontend');
    	$this->type("//input[@id='login']", 'taker1');
    	$this->type("//input[@id='password']", 'taker1');
    	$this->clickAndWait("//input[@id='connect']");
    	
    	// We arrive on the main menu. Create a new process, the first
    	// type in the available processes list.
    	$this->assertElementPresent("//h1[@id='welcome_message']");
    	$this->assertElementPresent("//div[@id='new_process']/ul/li[1]");
    	$this->clickAndWait("//div[@id='new_process']/ul/li[1]/a");
    	
    	// The process is now launched and the user ready to take the first
    	// item of the process. Let's randomly answer to each of them.
    	$inItem = true;
    	while ($inItem) {
    		$this->assertElementPresent('//iframe[1]');
    		$this->selectFrame('//iframe[1]');
    		
    		$itemBehaviour = new QTIBehaviour($this);
    		$itemBehaviour->run();
    		
    		if($this->isElementPresent("//h1[@id='welcome_message']")) {
    			$inItem = false;
    		}
    	}
    }
}
?>