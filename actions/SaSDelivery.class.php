<?php
/**
 * SaSDelivery Controller provide process services
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class SaSDelivery extends Delivery {
    
    /**
     * @see Delivery::__construct()
     */
    public function __construct() {
        $this->setSessionAttribute('currentExtension', 'taoDelivery');
		tao_helpers_form_GenerisFormFactory::setMode(tao_helpers_form_GenerisFormFactory::MODE_STANDALONE);
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
	 * Render the tree to exclude subjects of the delivery 
	 * @return void
	 */
	public function excludeSubjects(){
		$this->setData('uri', $this->getRequestParameter('uri'));
		$this->setData('classUri', $this->getRequestParameter('classUri'));
		
		$excludedSubjects = $this->service->getExcludedSubjects($this->getCurrentInstance());
		$excludedSubjects = array_map("tao_helpers_Uri::encode", $excludedSubjects);
		$this->setData('excludedSubjects', json_encode($excludedSubjects));
		
		$this->setView('subjects.tpl');
	}
		
	/**
	 * Render the tree to select the campaign 
	 * @return void
	 */
	public function selectCampaign(){
		$this->setData('uri', $this->getRequestParameter('uri'));
		$this->setData('classUri', $this->getRequestParameter('classUri'));
		
		$relatedCampaigns = $this->service->getRelatedCampaigns($this->getCurrentInstance());
		$relatedCampaigns = array_map("tao_helpers_Uri::encode", $relatedCampaigns);
		$this->setData('relatedCampaigns', json_encode($relatedCampaigns));
		
		$this->setView('delivery_campaign.tpl');
	}
}
?>