<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);
/**
 * A customised wfEngine ProcessBrowser module
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoDelivery
 * @subpackage actions
 */
class taoDelivery_actions_ProcessBrowser extends wfEngine_actions_ProcessBrowser{
	
	public function __construct(){
		parent::__construct();
		$this->autoRedirecting = false;
	}
	
	protected function redirectToMain(){
		$this->removeSessionAttribute("processUri");
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
	}
	
	public function loading(){
		$this->setView('itemLoading.tpl');
	}
	
	protected function autoredirectToIndex(){
		$this->redirectToIndex();
	}
    
    /**
     * Behaviour to adopt if the user is not allowed to access the current action.
     */
    protected function notAllowedRedirection() {
        $this->redirect(_url('index', 'DeliveryServerAuthentification', 'taoDelivery', array(
			'errorMessage' => urlencode(__('Access denied. Please renew your authentication.'))
		)));
    }
    
    /**
     * (non-PHPdoc)
     * @see wfEngine_actions_ProcessBrowser::index()
     */
    public function index() {
    	if ($this->hasRequestParameter('allowControl')) {
    		$this->setData('allowControl', $this->getRequestParameter('allowControl'));
    	}
        $resultServerCallOverride =  $this->hasRequestParameter('resultServerCallOverride') ? $this->getRequestParameter('resultServerCallOverride') : false;
    	
        parent::index();
        //intialize (start or resume) the result server for the current execution
        
        if (!($resultServerCallOverride)) {
            taoDelivery_models_classes_DeliveryServerService::singleton()->initResultServer($this->processExecution, $resultServerCallOverride);
        }
        
    }


}
?>
