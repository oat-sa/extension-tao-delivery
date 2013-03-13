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
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery/models/classes/interface.ResultServerInterface.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 01.03.2012, 11:34:37 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003838-includes begin
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003838-includes end

/* user defined constants */
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003838-constants begin
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003838-constants end

/**
 * Short description of class taoDelivery_models_classes_ResultServerInterface
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */
interface taoDelivery_models_classes_ResultServerInterface
{


    // --- OPERATIONS ---

    /**
     * Short description of method save
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function save();

    /**
     * Short description of method traceEvents
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function traceEvents();

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function evaluate();

} /* end of interface taoDelivery_models_classes_ResultServerInterface */

?>