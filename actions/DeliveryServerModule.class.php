<?php
class DeliveryServerModule extends Module
{
	
	public function __construct(){
		
		$GLOBALS['lang'] = $GLOBALS['default_lang'];
		
		if($this->_isAllowed()){
			tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService')->connectCurrentUser();
			
		}
		else{
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification', 'taoDelivery', array('errorMessage' => urlencode(__('Access denied. Please renew your authentication!')))));
		}
		
		//initialize I18N
		(Session::hasAttribute('ui_lang')) ? $uiLang = Session::getAttribute('ui_lang') : $uiLang = $GLOBALS['default_lang'];
		tao_helpers_I18n::init($uiLang);
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