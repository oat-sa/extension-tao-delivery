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
abstract class taoDelivery_models_classes_itemAccess_BaseAccessProvider
	implements taoDelivery_models_classes_itemAccess_ItemAccessProvider
{
	const DEFAULT_HTACCESS_CONTENT = 'php_flag engine off';
	
	public function __construct() {
	} 
	
	public function prepareProvider() {
		$this->writeHtaccessFile($this->getHtaccessContent());
	}
	
	public function cleanupProvider() {
		$this->writeHtaccessFile(self::DEFAULT_HTACCESS_CONTENT);
	}

	private function writeHtaccessFile($content) {
		$deliveryExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $compileBaseFolder = $deliveryExtension->getConstant('COMPILE_FOLDER');
		$filePath = $compileBaseFolder . '.htaccess';
    	if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
    		
    		// We first need to truncate.
    		ftruncate($fp, 0);
    		
    		fwrite($fp, $content);
    		@flock($fp, LOCK_UN);
    		@fclose($fp);
    	} else {
    		throw new common_exception_Error('Could not prepare item access provider '.get_class($this));
    	}
        return true;
	}
	
	/**
	 * default htaccess content
	 */
	protected function getHtaccessContent() {
		return self::DEFAULT_HTACCESS_CONTENT;
	}

}

?>