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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\taoDelivery\model;

use oat\oatbox\AbstractRegistry;
use oat\oatbox\service\ConfigurableService;

/**
 * Service is used to register different implementations of AssignmentService
 *
 * @access public
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 * @package taoDelivery
 */
class AssignmentServiceRegistry extends AbstractRegistry
{
    const CONFIG_ID = 'assignmentServiceRegistry';

    /**
     * @var array
     */
    private $services = [];

    /**
     * @return string
     */
    public function getConfigId()
    {
        return self::CONFIG_ID;
    }

    /**
     * @return \common_ext_Extension
     */
    public function getExtension()
    {
        return \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
    }

    /**
     * Register an assignment service
     *
     * @param string $key fully qualified class name
     * @param ConfigurableService $implementation
     * @return boolean true if registered
     * @throws \Exception
     */
    public function register($key, ConfigurableService $implementation)
    {
        if (!$implementation instanceof AssignmentService) {
            throw new \Exception(__('Service must implement "\oat\taoDelivery\model\AssignmentService" interface'));
        }
        self::getRegistry()->set($key, [
            'class' => get_class($implementation),
            'options' => $implementation->getOptions(),
        ]);
        return true;
    }

    /**
     * @param string $id
     * @return AssignmentService
     */
    public function get($id)
    {
        if (!isset($this->services['id'])) {
            $serviceConf = parent::get($id);
            $this->services['id'] = new $serviceConf['class']($serviceConf['options']);
        }
        return $this->services['id'];
    }
}