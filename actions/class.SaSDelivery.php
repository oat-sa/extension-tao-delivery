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

/**
 * SaSDelivery Controller provide process services
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_actions_SaSDelivery extends taoDelivery_actions_Delivery {
    
	protected function getClassService() {
		return taoDelivery_models_classes_DeliveryService::singleton();
	}
	
	/**
	 * Render the tree to exclude subjects of the delivery 
	 * @return void
	 */
	public function excludeSubjects(){
		//define the subjects excluded from the current delivery	
		$property = new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP);
		$tree = tao_helpers_form_GenerisTreeForm::buildTree($this->getCurrentInstance(), $property);
		$tree->setData('id', 'subject'); // used in css
		$tree->setData('title', __('Select test takers to be <b>excluded</b>'));
		$this->setData('tree', $tree->render());
		$this->setView('sas'.DIRECTORY_SEPARATOR.'generisTreeSelect.tpl', 'tao');
	}

}
?>