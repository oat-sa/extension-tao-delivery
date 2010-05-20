<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 20.05.2010, 17:34:50 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide service on user management
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('tao/models/classes/class.UserService.php');

/* user defined includes */
// section 10-11-2-22-1f35d42c:128b4acd65a:-8000:0000000000002420-includes begin
// section 10-11-2-22-1f35d42c:128b4acd65a:-8000:0000000000002420-includes end

/* user defined constants */
// section 10-11-2-22-1f35d42c:128b4acd65a:-8000:0000000000002420-constants begin
// section 10-11-2-22-1f35d42c:128b4acd65a:-8000:0000000000002420-constants end

/**
 * Short description of class taoDelivery_models_classes_UserService
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function initRoles()
    {
        // section 10-11-2-22-1f35d42c:128b4acd65a:-8000:0000000000002421 begin
		$this->allowedRoles = array('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject', INSTANCE_ROLE_SUBJECT);
        // section 10-11-2-22-1f35d42c:128b4acd65a:-8000:0000000000002421 end
    }

    /**
     * Short description of method loginUser
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function loginUser($login, $password = '')
    {
        $returnValue = (bool) false;

        // section 10-11-2-22-1f35d42c:128b4acd65a:-8000:0000000000002423 begin
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
        // section 10-11-2-22-1f35d42c:128b4acd65a:-8000:0000000000002423 end

        return (bool) $returnValue;
    }

} /* end of class taoDelivery_models_classes_UserService */

?>