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
 */

namespace oat\taoDelivery\helper;

use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecution;

/**
 * Helper to render the delivery form on the group page
 *
 * @author joel bout, <joel@taotesting.com>
 * @package taoDelivery

 */
class Delivery
{
    public const ID = 'id';

    public const LABEL = 'label';

    public const AUTHORIZED = 'TAO_DELIVERY_TAKABLE';

    public const DESCRIPTION = 'description';

    public const LAUNCH_URL = 'launchUrl';

    public static function buildFromAssembly($assignment, User $user)
    {
        $data = [
            self::ID => $assignment->getDeliveryId(),
            self::LABEL => $assignment->getLabel(),
            self::LAUNCH_URL => _url(
                'initDeliveryExecution',
                'DeliveryServer',
                null,
                [
                    'uri' => $assignment->getDeliveryId(),
                ]
            ),
            self::DESCRIPTION => $assignment->getDescriptionStrings(),
            self::AUTHORIZED => $assignment->isStartable()
        ];
        return $data;
    }

    public static function buildFromDeliveryExecution(DeliveryExecution $deliveryExecution)
    {
        $data = [];
        $data[self::ID] = $deliveryExecution->getIdentifier();
        $data[self::LABEL] = $deliveryExecution->getLabel();
        $data[self::LAUNCH_URL] = _url(
            'runDeliveryExecution',
            'DeliveryServer',
            null,
            [
                'deliveryExecution' => $deliveryExecution->getIdentifier(),
            ]
        );
        $data[self::DESCRIPTION] = [
            __("Started at %s", \tao_helpers_Date::displayeDate($deliveryExecution->getStartTime())),
        ];
        $data[self::AUTHORIZED] = true;
        return $data;
    }
}
