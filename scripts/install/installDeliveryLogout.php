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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoDelivery\scripts\install;

class installDeliveryLogout extends \oat\oatbox\extension\InstallAction 
{
    
    public function __invoke($params) {
        
        /*@var $routeService \oat\tao\model\mvc\DefaultUrlService */
        $routeService = $this->getServiceManager()->get(\oat\tao\model\mvc\DefaultUrlService::SERVICE_ID);
        $routeService->setRoute('logoutDelivery',
                    [
                        'ext'        => 'taoDelivery',
                        'controller' => 'DeliveryServer',
                        'action'     => 'logout',
                        'redirect'   =>
                            [
                                'class'   => \oat\tao\model\mvc\DefaultUrlModule\TaoActionResolver::class,
                                'options' => [
                                    'action' => 'entry',
                                    'controller' => 'Main',
                                    'ext' => 'tao'
                                ]
                            ],
                    ]
                );
        $this->getServiceManager()->register(\oat\tao\model\mvc\DefaultUrlService::SERVICE_ID , $routeService);
        
    }
    
}
