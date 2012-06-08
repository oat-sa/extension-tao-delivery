<?php
class taoDelivery_actions_DeliveryServerAuthentification extends Module
{
	public function index()
	{
		
		if($this->hasRequestParameter('errorMessage')){
			$this->setData('errorMessage',$this->getRequestParameter('errorMessage'));
		}
		
		$userService = taoDelivery_models_classes_UserService::singleton();
		
		$myLoginFormContainer = new wfEngine_actions_form_Login();
		$myForm = $myLoginFormContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				if($userService->loginUser($values['login'], md5($values['password']))){//no md5 yet for subject password
					common_Logger::d('good');
					$this->redirect(_url('index', 'DeliveryServer'));
				}
				else{
					common_Logger::d('bad');
					$this->setData('errorMessage', __('No account match the given login / password'));
				}
			}
		}
		
		tao_helpers_Scriptloader::addJsFile(BASE_WWW . 'js/login.js');
		$this->setData('form', $myForm->render());
		$this->setView('login.tpl');
	}


	public function logout(){
		unset($_SESSION['taoqual.authenticated']);
		session_destroy();
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification'));
	}
}
?>
