<?php
class DeliveryServerAuthentification extends Module
{
	public function index()
	{

		if($this->hasRequestParameter('errorMessage')){
			$this->setData('errorMessage',$this->getRequestParameter('errorMessage'));
		}
		
		$userService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService');
		
		$myLoginFormContainer = new wfEngine_actions_form_Login();
		$myForm = $myLoginFormContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				if($userService->loginUser($values['login'], md5($values['password']))){//no md5 yet for subject password
					$this->redirect(_url('index', 'DeliveryServer'));
				}
				else{
					$this->setData('errorMessage', __('No account match the given login / password'));
				}
			}
		}
		
		$this->setData('form', $myForm->render());
		$this->setView('login.tpl');
	}


	public function logout(){
		unset($_SESSION['taoqual.authenticated']);
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification'));
	}
}
?>
