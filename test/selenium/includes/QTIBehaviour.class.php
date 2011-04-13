<?php
require_once(dirname(__FILE__) . '/ItemBehaviour.class.php');

class QTIBehaviour extends ItemBehaviour {
	
	private $interactions = array();
	
	public function __construct($selenium) {
		parent::__construct($selenium);
		$this->discoverInteractions();
	}
	
	public function getInteractions() {
		return $this->interactions;
	}
	
	public function setInteractions(array $interactions) {
		$this->interactions = $interactions;
	}
	
	public function run() {
		$selenium = $this->getSelenium();
		
		foreach ($this->getInteractions() as $i) {
			$i->run();
		}
	}
	
	private function discoverInteractions() {
		$selenium = $this->getSelenium();
		
		if (($count = $selenium->getXpathCount(QTIChoiceBehaviour::getXPath())) > 0) {
			for ($i = 0; $i < $count; $i++) {
				$this->addInteraction(new QTIChoiceBehaviour($selenium, $i));
			}
		}
		
		if (($count = $selenium->getXpathCount(QTIMatchBehaviour::getXPath())) > 0) {
			for ($i = 0; $i < $count; $i++) {
				$this->addInteraction(new QTIMatchBehaviour($selenium, $i));
			}
		}
	}
	
	private function addInteraction(QTIInteractionBehaviour $behaviour) {
		$interactions = $this->getInteractions();
		$interactions[] = $behaviour;
		$this->setInteractions($interactions);
	}
}
?>