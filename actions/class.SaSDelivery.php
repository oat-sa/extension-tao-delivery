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
/**
 * SaSDelivery Controller provide process services
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_actions_SaSDelivery extends taoDelivery_actions_Delivery {
    
    /**
     * @see Delivery::__construct()
     */
    public function __construct() {
    	tao_helpers_Context::load('STANDALONE_MODE');
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
			$this->setData('includedView', DIR_VIEWS . 'templates/' . $identifier);
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
		
		$excludedSubjects = tao_helpers_Uri::encodeArray($this->service->getExcludedSubjects($this->getCurrentInstance()), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
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
		
		$relatedCampaigns = tao_helpers_Uri::encodeArray($this->service->getRelatedCampaigns($this->getCurrentInstance()), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('relatedCampaigns', json_encode($relatedCampaigns));
		
		$this->setView('delivery_campaign.tpl');
	}
}
?>