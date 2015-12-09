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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 * 
 */
namespace oat\taoDelivery\model;

use oat\oatbox\user\User;
/**
 * Basic Assignment
 *
 * @author Open Assessment Technologies SA
 * @package taoFrontOffice
 * @license GPL-2.0
 *
 */
class Assignment {
    
    private $deliveryId;
    
    private $label;
    
    private $desc;
    
    private $startable;
    
    private $launchParams;
    
    public function __construct($deliveryId, $userId, $label, $desc, $startable, $launchParams)
    {
        $this->deliveryId = $deliveryId;
        $this->label = $label;
        $this->desc = $desc;
        $this->startable = $startable;
        $this->launchParams = $launchParams;
        
    }
    
    public function getDeliveryId()
    {
        return $this->deliveryId;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function getDescriptionStrings()
    {
        return $this->desc;
    }
    
    public function isStartable()
    {
        return $this->startable;
    }
    
    public function getLaunchParameters()
    {
        return $this->launchParams;
    }
    
}