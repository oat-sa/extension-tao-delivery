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
 * requires the phpredis library
 *
 * @access public
 * @author @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes_runner
 */
class taoDelivery_models_classes_itemVariables_RedisPersistence
	implements taoDelivery_models_classes_itemVariables_VariablePersistence
{
	private $server = null;
	
	public function __construct() {
		$this->server = new Redis();
		if ($this->server == false) {
			throw new common_Exception("Redis php module not found");
		} 
		if (!$this->server->connect('127.0.0.1')) {
			throw new common_Exception("Unable to connect to redis server");
		};
	}
	
	public function set($user, $serial, $data) {
		$redisSerial = $user->getUri().'_'.$serial;
		$dataString = json_encode($data, true);
		return $this->server->set($redisSerial, $dataString);
	}
	
	public function get($user, $serial) {
		$redisSerial = $user->getUri().'_'.$serial;
		$returnValue = $this->server->get($redisSerial);
		if ($returnValue === false && !$this->has($user, $serial)) {
			$returnValue = null; 
		} else {
			$returnValue = json_decode($returnValue, true);
		}
		return $returnValue;
	}
	
	public function has($user, $serial) {
		$redisSerial = $user->getUri().'_'.$serial;
		return $this->server->exists($redisSerial);
	}
	
	public function del($user, $serial) {
		$redisSerial = $user->getUri().'_'.$serial;
		return $this->server->del($redisSerial);
	}
  
  	private function getPath($user, $serial) {
  		return STORAGE_FOLDER . md5($user->getUri()) . DIRECTORY_SEPARATOR . md5($serial);
  	}
}

?>