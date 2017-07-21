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
namespace oat\taoDelivery\model\authorization\strategy;

use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\authorization\AuthorizationProvider;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * Verifies that the current state of a delivery execution allows resuming
 */
class StateValidation extends ConfigurableService  implements AuthorizationProvider
{
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\authorization\AuthorizationProvider::verifyStartAuthorization()
     */
    public function verifyStartAuthorization($deliveryId, User $user)
    {
        // nothign to check, no state yet
    }
    
    /**
     * Verify that a given delivery execution is allowed to be executed
     *
     * @param DeliveryExecutionInterface $deliveryExecution
     * @param User $user
     * @throws \common_exception_Unauthorized
    */
    public function verifyResumeAuthorization(DeliveryExecutionInterface $deliveryExecution, User $user)
    {
        $stateId = $deliveryExecution->getState()->getUri();
        if (!in_array($stateId, $this->getResumableStates())) {
            \common_Logger::w('Unexpected state "'.$stateId);
            throw new \common_exception_Unauthorized();
        }
    }
    
    public function getResumableStates()
    {
        return array(DeliveryExecution::STATE_ACTIVE, DeliveryExecution::STATE_PAUSED);
    }
}
