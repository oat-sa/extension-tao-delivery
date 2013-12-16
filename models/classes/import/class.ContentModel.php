<?php

/**  
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * An imported delivery Assembly model
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes_simple
 */
class taoDelivery_models_classes_import_ContentModel implements taoDelivery_models_classes_ContentModel
{

    /**
     * The simple delivery content extension
     *
     * @var common_ext_Extension
     */
    private $extension;

    public function __construct()
    {
    }

    public function getClass()
    {
        return new core_kernel_classes_Class(CLASS_DELIVERY_CONTENT_ASSEMBLY);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring(core_kernel_classes_Resource $content)
    {
        return "";
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function createContent($tests = array()) {
        return $this->getClass()->createInstance();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function delete(core_kernel_classes_Resource $content) {
    	$content->delete();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::cloneContent()
     */
    public function cloneContent(core_kernel_classes_Resource $content) {
        throw new common_Exception(__FUNCTION__.' called on imported Assembly');
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeDeliveryLabel(core_kernel_classes_Resource $delivery) {
        // nothing to do
    }

    public function getCompilerClass() {
        throw new common_Exception(__FUNCTION__.' called on imported Assembly');
    }
    
}