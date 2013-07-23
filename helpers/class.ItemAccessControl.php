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
 * Grants Access to compiled data
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage helpers
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_helpers_ItemAccessControl
{
	const CONFIG_KEY = 'ITEM_ACCESS_CONTROLLER';

	public static function getAccessUrl($delivery, $test, $item, $lang) {
		return self::getAccessProvider()->getAccessUrl($delivery, $test, $item, $lang);
	}
	
	public static function setAccessProvider($providerClass) {
		if (class_exists($providerClass) && in_array('taoDelivery_models_classes_itemAccess_ItemAccessProvider', class_implements($providerClass))) {
			$old = self::getAccessProvider();
			if (!is_null($old)) {
				$old->cleanupProvider();
			}
			$new = new $providerClass();
			$new->prepareProvider();
			$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
			$ext->setConfig(self::CONFIG_KEY, $providerClass);
		} else {
			throw new common_Exception($providerClass.' is not a valid item access provider');
			
		}
	}
	
	public static function getAccessProvider() {
		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
		$className = $ext->getConfig(self::CONFIG_KEY);
		return !is_null($className) && class_exists($className)
			? new $className()
			: null;
	}

}

?>