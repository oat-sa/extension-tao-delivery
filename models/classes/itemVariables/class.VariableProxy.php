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
class taoDelivery_models_classes_itemVariables_VariableProxy
    extends tao_models_classes_Service
{
	private $implementation;
	
	protected function __construct() {
		$this->implementation = new taoDelivery_models_classes_itemVariables_RedisPersistence();
		parent::__construct();
	}
	
	public function set($user, $serial, $data) {
		return $this->getImplementation()->set($user, $serial, $data);
	}
	
	public function has($user, $serial) {
		return $this->getImplementation()->has($user, $serial);
	}
	
	public function get($user, $serial) {
		return $this->getImplementation()->get($user, $serial);
	}
	
	public function del($user, $serial) {
		return $this->getImplementation()->del($user, $serial);
	}
  
  	private function getImplementation() {
  		return $this->implementation;
  	}
}

?>