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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDelivery\model;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\ServiceProxy;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;

/**
 * Service to count the attempts to pass the test.
 *
 * @access public
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 * @package taoDelivery
 */
class AttemptService extends ConfigurableService implements AttemptServiceInterface
{

    const OPTION_STATES_TO_EXCLUDE = 'states_to_exclude';

    /**
     * @inheritdoc
     */
    public function getAttempts($deliveryId, User $user)
    {
        $executions = $this->getServiceLocator()->get(ServiceProxy::SERVICE_ID)
            ->getUserExecutions(new \core_kernel_classes_Resource($deliveryId), $user->getIdentifier());
        return $this->filterStates($executions);
    }

    /**
     * @inheritdoc
     */
    public function setStatesToExclude(array $deliveryExecutionsStates)
    {
        $this->setOption(self::OPTION_STATES_TO_EXCLUDE, $deliveryExecutionsStates);
    }

    /**
     * @return array|mixed
     */
    public function getStatesToExclude()
    {
        $statesToExclude = $this->getOption(self::OPTION_STATES_TO_EXCLUDE);
        if (!is_array($statesToExclude)) {
            $statesToExclude = [];
        }
        return $statesToExclude;
    }

    /**
     * @param DeliveryExecutionInterface[] $executions
     * @return DeliveryExecutionInterface[]
     */
    protected function filterStates(array $executions = [])
    {
        $statesToExclude = $this->getStatesToExclude();
        if (!empty($statesToExclude)) {
            $result = array_filter($executions, function ($execution) use ($statesToExclude) {
                return !in_array($execution->getState()->getUri(), $statesToExclude);
            });
        } else {
            $result = $executions;
        }
        return $result;
    }
}
