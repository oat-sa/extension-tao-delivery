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
 * TAO - taoDelivery\models\classes\class.UserService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.12.2010, 16:46:53 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide service on user management
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('tao/models/classes/class.UserService.php');

/* user defined includes */
// section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D31-includes begin
// section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D31-includes end

/* user defined constants */
// section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D31-constants begin
// section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D31-constants end

/**
 * Short description of class taoDelivery_models_classes_UserService
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_UserService
    extends tao_models_classes_UserService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initRoles
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function initRoles()
    {
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D32 begin
		$this->allowedRoles = array(INSTANCE_ROLE_DELIVERY => new core_kernel_classes_Resource(INSTANCE_ROLE_DELIVERY));
        // section -64--88-1-32-2901cf54:12cfee72c73:-8000:0000000000004D32 end
    }

} /* end of class taoDelivery_models_classes_UserService */

?>