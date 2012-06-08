<?php
class taoDelivery_actions_DeliveryServerModule extends tao_actions_CommonModule
{
	
	public function __construct(){
		
		if(!$this->_isAllowed()){
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification', 'taoDelivery', array('errorMessage' => urlencode(__('Access denied. Please renew your authentication!')))));
		}
		
	}

}
?>