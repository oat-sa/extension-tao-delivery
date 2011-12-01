<?php
class taoDelivery_actions_DeliveryServerModule extends Module
{
	
	public function __construct(){
		
		if($this->_isAllowed()){
			taoDelivery_models_classes_UserService::singleton()->connectCurrentUser();
			
		}
		else{
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification', 'taoDelivery', array('errorMessage' => urlencode(__('Access denied. Please renew your authentication!')))));
		}
		
	}
	
	/**
	 * Check if the current user is allowed to acces the request
	 * Override this method to allow/deny a request
	 * @return boolean
	 */
	protected function _isAllowed(){
		return (isset($_SESSION['taoqual.authenticated']) && core_kernel_users_Service::singleton()->isASessionOpened());	//if a user is logged in
	}
}
?>