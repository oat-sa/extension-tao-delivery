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
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 * 
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoDelivery\model\AssignmentAggregator;


use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\User;
use oat\taoDelivery\model\Assignment;

class UnionAssignmentService extends ConfigurableService implements UnionAssignmentInterface
{
    
    public function getInternalServices()
    {
        $services = $this->getOption('services');
        foreach ($services as $service) {
            $this->propagate($service);
        }
        return $services;
    }
    
    /**
     * Returns the deliveries available to a user
     *
     * @param User $user
     * @return Assignment[] list of deliveries
     */
    public function getAssignments(User $user)
    {
        $assignments = [];
        foreach ($this->getInternalServices() as $service) {
            $assignments = array_merge($assignments, $service->getAssignments($user));
        }

        return $assignments;
    }

    /**
     * Returns the ids of users assigned to a delivery
     *
     * @param string|\core_kernel_classes_Resource $deliveryId form|delivery instance or form id
     * @return string[] ids of users
     */
    public function getAssignedUsers($deliveryId)
    {
        $users = [];
        foreach ($this->getInternalServices() as $service) {
            $users = array_merge($users, $service->getAssignedUsers($deliveryId));
        }

        return $users;
    }

    public function isDeliveryExecutionAllowed($deliveryIdentifier, User $user)
    {
        $result = false;
        foreach ($this->getInternalServices() as $service) {
            if($service->isDeliveryExecutionAllowed($deliveryIdentifier, $user)) {
                return true;
            }
        }

        return $result;
    }

    public function getRuntime($deliveryId)
    {
        foreach ($this->getInternalServices() as $service) {
            $runtime = $service->getRuntime($deliveryId);
            if ($runtime) {
                return $runtime;
            }
        }
        
        return false;
    }
}
