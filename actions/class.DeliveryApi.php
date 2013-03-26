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
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */

class taoDelivery_actions_DeliveryApi extends tao_actions_Api {
	
/**
	 * Get the folder where is the current compiled item
	 * @param array $executionEnvironment
	 * @return string
	 */
	protected function getCompiledFolder($executionEnvironment){

		$returnValue = null;
		
		if( isset($executionEnvironment[TAO_ITEM_CLASS]['uri']) && 
		 	isset($executionEnvironment[TAO_TEST_CLASS]['uri']) &&
		 	isset($executionEnvironment[TAO_DELIVERY_CLASS]['uri'])
		 	){
					
			$item 		= new core_kernel_classes_Resource($executionEnvironment[TAO_ITEM_CLASS]['uri']);
			$test 		= new core_kernel_classes_Resource($executionEnvironment[TAO_TEST_CLASS]['uri']);
			$delivery 	= new core_kernel_classes_Resource($executionEnvironment[TAO_DELIVERY_CLASS]['uri']);
			$languages	= array(core_kernel_classes_Session::singleton()->getDataLanguage(), DEFAULT_LANG);
			
			try {
				$returnValue = taoDelivery_models_classes_DeliveryService::singleton()->getCompiledItemFolder(
					$delivery, $test, $item, $languages
				);
			} catch (common_Exception $e) {
				// do nothing if missing, to emulate former behaviour
			}

		}
		
		return $returnValue;
	}
}
?>