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

use oat\taoDelivery\model\execution\implementation\KeyValueService;
use oat\taoDelivery\models\classes\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecution as InterfaceDeliveryExecution;
/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @deprecated  please use oat\taoDelivery\model\execution\KeyValueService
 */
class taoDelivery_models_classes_execution_KeyValueService extends KeyValueService
{

    public function getUserExecutions(core_kernel_classes_Resource $compiled, $userUri)
    {
        $activ = $this->getDeliveryExecutionsByStatus($userUri, InterfaceDeliveryExecution::STATE_ACTIVE);
        $finished = $this->getDeliveryExecutionsByStatus($userUri, InterfaceDeliveryExecution::STATE_FINISHIED);

        $returnValue = array();
        foreach (array_merge($activ, $finished) as $de) {
            if ($compiled->equals($de->getDelivery())) {
                $returnValue[] = $de;
            }
        }
        return $returnValue;
    }
}
