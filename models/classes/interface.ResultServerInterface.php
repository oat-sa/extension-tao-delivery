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