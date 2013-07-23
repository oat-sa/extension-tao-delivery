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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * Persistence for the item delivery service
 *
 * @access public
 * @author @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes_runner
 */
class taoDelivery_models_classes_itemVariables_FileSystemPersistence
	implements taoDelivery_models_classes_itemVariables_VariablePersistence
{
	public function __construct() {
		
	}
	
	public function set($user, $serial, $data) {
		$string = "<? return ".common_Utils::toPHPVariableString($data).";?>";
		$filePath = $this->getPath($user, $serial);
		$folderPath = dirname($filePath);
		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0740, true);
		}
    	if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
    		
    		// We first need to truncate.
    		ftruncate($fp, 0);
    		
    		fwrite($fp, $string);
    		@flock($fp, LOCK_UN);
    		@fclose($fp);
    	}
        return true;
	}
	
	public function has($user, $serial) {
		return file_exists($this->getPath($user, $serial));
	}
	
	public function get($user, $serial) {
		if ($this->has($user, $serial)) {
			return include @$this->getPath($user, $serial);
		} else {
			return null;
		}
	}
	
	public function del($user, $serial) {
		if ($this->has($user, $serial)) {
			return unlink(@$this->getPath($user, $serial));
		} else {
			return false;
		}
	}
  
  	private function getPath($user, $serial) {
  		return STORAGE_FOLDER . md5($user->getUri()) . DIRECTORY_SEPARATOR . md5($serial);
  	}
}

?>