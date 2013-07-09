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
 * An error structure generated during compilation
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_CompilationErrorStructure
    extends common_report_ErrorElement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
    const DELIVERY_ERROR_TYPE = 'delivery';
    const TEST_ERROR_TYPE = 'test';
    
    /**
     * Type of the compilation error,
     * delivery or test
     * 
     * @var string
     */
    private $type;
    
    /**
     * poorly defined error array
     * @var array
     */
    private $error = array();

    // --- OPERATIONS ---

    /**
     * Create a new error structure as defined by the delivery compiler 
     * 
     * @param string $type
     * @param array $struct
     */
    public function __construct($type, $struct) {
        parent::__construct('');
        $this->type = $type;
        $this->error = $struct;
    }
    
    /**
     * Returns whenever the error was a test or delivery error
     * 
     * @return String
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns a custom error structure
     * 
     * @return array
     */
    public function getStructure() {
        return $this->error;
    }

} 

?>