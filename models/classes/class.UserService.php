<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery\models\classes\class.UserService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.12.2010, 16:46:53 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide service on user management
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('tao/models/classes/class.UserService.php');

/* user defined includes */
// section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D31-includes begin
// section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D31-includes end

/* user defined constants */
// section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D31-constants begin
// section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D31-constants end

/**
 * Short description of class taoDelivery_models_classes_UserService
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_UserService
    extends tao_models_classes_UserService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initRoles
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function initRoles()
    {
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D32 begin
		$this->allowedRoles = array(CLASS_ROLE_SUBJECT);
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D32 end
    }

    /**
     * Short description of method loginUser
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function loginUser($login, $password = '')
    {
        $returnValue = (bool) false;

        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D34 begin
		if(parent::loginUser($login, $password)){
        	
        	if($this->connectCurrentUser()){
	        	$currentUser = $this->getCurrentUser();
	        	if(!is_null($currentUser)){
	        		
	        		$login 			= (string)$currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
	        		$password 		= (string)$currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
					try{
	        			$dataLang 	= (string)$currentUser->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_DEFLG));
					}
					catch(common_Exception $ce){
						$dataLang 	= 'EN';
					}
					
	        		//log in the wf engines
					$_SESSION["WfEngine"] 		= WfEngine::singleton($login, $password);
					$user = WfEngine::singleton()->getUser();
					if($user == null) {
						$returnValue=  false;
					}
					else{
						$_SESSION["userObject"] 	= $user;
							
						// Taoqual authentication and language markers.
						$_SESSION['taoqual.authenticated'] 		= true;
						$_SESSION['taoqual.lang']				= $dataLang;
						$_SESSION['taoqual.serviceContentLang'] = $dataLang;
						$_SESSION['taoqual.userId']				= $login;
						
						$returnValue = true;
					}
	        	}
        	}
        }
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D34 end

        return (bool) $returnValue;
    }

} /* end of class taoDelivery_models_classes_UserService */

?>