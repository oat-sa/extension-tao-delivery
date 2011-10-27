<?php

error_reporting(E_ALL);

class taoDelivery_actions_ProcessBrowser extends wfEngine_actions_ProcessBrowser{
	
	public function __construct(){
		parent::__construct();
		$this->autoRedirecting = false;
	}
	
	protected function redirectToMain(){
		Session::removeAttribute("processUri");
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
	}
	
	public function loading(){
		$this->setView('itemLoading.tpl');
	}
	
	protected function autoredirectToIndex(){
		$this->redirectToIndex();
	}
}
?>
