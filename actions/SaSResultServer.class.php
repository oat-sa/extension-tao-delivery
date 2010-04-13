<?php
/**
 * SaSResultServer Controller provide process services
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class SaSResultServer extends ResultServer {
    
    /**
     * @see ResultServer::__construct()
     */
    public function __construct() {
        $this->setSessionAttribute('currentExtension', 'taoDelivery');
		tao_helpers_form_GenerisFormFactory::setMode(tao_helpers_form_GenerisFormFactory::MODE_STANDALONE);
		parent::__construct();
    }
    
 	/**
     * Give the auth to the workflow engine
     * @return boolean
     */
    protected function _isAllowed(){
    	return isset($_SESSION['taoqual.authenticated']);
    }
		
	/**
     * @see TaoModule::setView()
     */
    public function setView($identifier, $useMetaExtensionView = false) {
		if($useMetaExtensionView){
			$this->setData('includedView', $identifier);
		}
		else{
			$this->setData('includedView', BASE_PATH . '/' . DIR_VIEWS . $GLOBALS['dir_theme'] . $identifier);
		}
		parent::setView('sas.tpl', true);
    }
	
	/**
     * overrided to prevent exception: 
     * if no class is selected, the root class is returned 
     * @see TaoModule::getCurrentClass()
     * @return core_kernel_class_Class
     */
    protected function getCurrentClass() {
        if($this->hasRequestParameter('classUri')){
        	return parent::getCurrentClass();
        }
		return $this->getRootClass();
    }
	
	/**
	 * Render the tree to select the deliveries 
	 * @return void
	 */
	public function selectDeliveries(){
		$this->setData('uri', $this->getRequestParameter('uri'));
		$this->setData('classUri', $this->getRequestParameter('classUri'));
		
		$relatedDeliveries = $this->service->getRelatedDeliveries($this->getCurrentInstance());
		$relatedDeliveries = array_map("tao_helpers_Uri::encode", $relatedDeliveries);
		$this->setData('relatedDeliveries', json_encode($relatedDeliveries));
		
		$this->setData('index', '1');
		$this->setView('delivery.tpl');
	}
}
?>