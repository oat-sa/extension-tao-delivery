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
 * TAO - taoDelivery\models\classes\class.DeliveryProcessChecker.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 20.11.2012, 10:24:32 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfAuthoring_models_classes_ProcessChecker
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('wfAuthoring/models/classes/class.ProcessChecker.php');

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
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryProcessChecker
    extends wfAuthoring_models_classes_ProcessChecker
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method check
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array checkList
     * @return boolean
     */
    public function check($checkList = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FC4 begin
		$returnValue = parent::check(array('checkInitialActivity', 'checkNoIsolatedConnector'));
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FC4 end

        return (bool) $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryProcessChecker */

?>