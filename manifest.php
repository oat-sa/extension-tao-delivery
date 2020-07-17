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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

use oat\tao\model\user\TaoRoles;
use oat\taoDelivery\controller\DeliveryServer;
use oat\taoDelivery\scripts\install\installDeliveryLogout;
use oat\taoDelivery\scripts\install\installDeliveryFields;
use oat\taoDelivery\scripts\install\GenerateRdsDeliveryExecutionTable;
use oat\taoDelivery\scripts\install\RegisterServiceContainer;
use oat\taoDelivery\scripts\install\RegisterWebhookEvents;
use oat\taoDelivery\scripts\install\RegisterFrontOfficeEntryPoint;
use oat\taoDelivery\controller\RestExecution;

$extpath = __DIR__ . DIRECTORY_SEPARATOR;

return [
    'name' => 'taoDelivery',
    'label' => 'Delivery core extension',
    'description' => 'TAO delivery extension manges the administration of the tests',
    'license' => 'GPL-2.0',
    'version' => '14.18.2',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'requires' => [
        'tao' => '>=44.0.0',
        'generis' => '>=12.15.0',
        'taoResultServer' => '>=5.0.0'
    ],
    'install' => [
        'php' => [
            RegisterFrontOfficeEntryPoint::class,
            installDeliveryLogout::class,
            installDeliveryFields::class,
            GenerateRdsDeliveryExecutionTable::class,
            RegisterServiceContainer::class,
            RegisterWebhookEvents::class
        ]
    ],
    'update' => 'oat\\taoDelivery\\scripts\\update\\Updater',
    'acl' => [
        ['grant', TaoRoles::DELIVERY, DeliveryServer::class],
        ['grant', TaoRoles::ANONYMOUS, DeliveryServer::class . '@logout'],
        ['grant', TaoRoles::REST_PUBLISHER, RestExecution::class],
    ],
    'routes' => [
        '/taoDelivery' => 'oat\\taoDelivery\\controller'
    ],
    'constants' => [
        # views directory, required for js
        "DIR_VIEWS"             => $extpath . "views" . DIRECTORY_SEPARATOR,

        #BASE URL (usually the domain root), required for js
        'BASE_URL'              => ROOT_URL . 'taoDelivery/',
    ]
];
