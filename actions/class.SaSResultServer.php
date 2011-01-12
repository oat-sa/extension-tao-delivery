<?php
/**
 * SaSResultServer Controller provide process services
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_actions_SaSResultServer extends taoDelivery_actions_ResultServer {
    
    /**
     * @see ResultServer::__construct()
     */
    public function __construct() {
    	tao_helpers_Context::load('STANDALONE_MODE');
        $this->setSessionAttribute('currentExtension', 'taoDelivery');
		parent::__construct();
    }
    
		
	/**
     * @see TaoModule::setView()
     */
    public function setView($identifier, $useMetaExtensionView = false) {
		if(tao_helpers_Request::isAjax()){
			return parent::setView($identifier, $useMetaExtensionView);
		}
    	if($useMetaExtensionView){
			$this->setData('includedView', $identifier);
		}
		else{
			$this->setData('includedView', BASE_PATH . '/' . DIR_VIEWS . $GLOBALS['dir_theme'] . $identifier);
		}
		return parent::setView('sas.tpl', true);
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
		
		$relatedDeliveries = tao_helpers_Uri::encodeArray($this->service->getRelatedDeliveries($this->getCurrentInstance()), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('relatedDeliveries', json_encode($relatedDeliveries));
		
		$this->setData('index', '1');
		$this->setView('delivery.tpl');
	}
}
?>