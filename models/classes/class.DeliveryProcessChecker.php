<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery\models\classes\class.DeliveryProcessChecker.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 22.02.2011, 15:27:51 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfEngine_models_classes_ProcessChecker
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('wfEngine/models/classes/class.ProcessChecker.php');

/* user defined includes */
// section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FC0-includes begin
// section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FC0-includes end

/* user defined constants */
// section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FC0-constants begin
// section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FC0-constants end

/**
 * Short description of class taoDelivery_models_classes_DeliveryProcessChecker
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryProcessChecker
    extends wfEngine_models_classes_ProcessChecker
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method check
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return boolean
     */
    public function check()
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FC4 begin
		$returnValue = parent::check(array('checkInitialActivity', 'checkNoIsolatedConnector'));
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FC4 end

        return (bool) $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryProcessChecker */

?>