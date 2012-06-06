<?php
class taoDelivery_actions_DeliveryServerModule extends tao_actions_CommonModule
{
	
	public function __construct(){
		
		if($this->_isAllowed()){
			taoDelivery_models_classes_UserService::singleton()->connectCurrentUser();
			
		}
		else{
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification', 'taoDelivery', array('errorMessage' => urlencode(__('Access denied. Please renew your authentication!')))));
		}
		
	}

}
?>