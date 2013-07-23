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
 * Grants direct Access to compiled data
 * This is the fastest implementation but
 * allows anyone access that guesses the path
 * access to the compiled delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes_itemAccess
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_itemAccess_DirectAccessProvider
	extends taoDelivery_models_classes_itemAccess_BaseAccessProvider
{
	public function getAccessUrl($delivery, $test, $item, $language) {
		$compiledFolder = taoDelivery_models_classes_DeliveryService::singleton()->getCompiledItemFolder(
			$delivery, $test, $item, array($language)
		);
		$compiledURL = tao_helpers_Uri::getUrlForPath($compiledFolder);
		return $compiledURL .'index.html';
	}
}

?>