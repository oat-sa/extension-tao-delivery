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
 * An Exception to be thrown when a problem occurs at item compliation time.
 * 
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package taoDelivery
 * @subpackage helpers
 *
 */
class taoDelivery_helpers_CompilationException{
	
	/**
	 * The item that caused the compilation error.
	 * 
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @access private
	 * @var core_kernel_classes_Resource
	 */
	private $item;
	
	/**
	 * Creates a new instance of taoDelivery_helpers_CompilationException.
	 * 
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param string msg The message of the exception.
	 * @param core_kernel_classes_Resource item The item that caused the compilation error.
	 */
	public function __construct($message, core_kernel_classes_Resource $item){
		parent::__construct($message);
		$this->setItem($item);
	}
	
	/**
	 * Get the item that caused the compilation error.
	 * 
	 * @access public
	 * @author Jerome Bogaerts <jerome@taotesting.com>
	 * @return core_kernel_classes_Resource
	 */
	public function getItem(){
		return $this->item;
	}
	
	/**
	 * Set the item that caused the compilation error.
	 * 
	 * @access protected
	 * @author Jerome Bogaerts <jerome@taotesting.com>
	 * @param core_kernel_classes_Resource $item An item.
	 */
	protected function setItem(core_kernel_classes_Resource $item){
		$this->item = $item;
	}
}
?>