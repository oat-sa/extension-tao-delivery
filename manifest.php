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
use oat\taoDelivery\scripts\install\RegisterServiceContainer;
use oat\taoDelivery\scripts\install\RegisterWebhookEvents;

$extpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tao' . DIRECTORY_SEPARATOR;

return [
    'name' => 'taoDelivery',
    'label' => 'Delivery core extension',
    'description' => 'TAO delivery extension manges the administration of the tests',
    'license' => 'GPL-2.0',
    'version' => '14.13.0',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'requires' => [
        'tao' => '>=41.10.0',
        'generis' => '>=12.15.0',
        'taoResultServer' => '>=5.0.0'
    ],
    'install' => [
        'php' => [
            __DIR__ . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'registerEntryPoint.php',
            \oat\taoDelivery\scripts\install\installDeliveryLogout::class,
            \oat\taoDelivery\scripts\install\installDeliveryFields::class,
            \oat\taoDelivery\scripts\install\GenerateRdsDeliveryExecutionTable::class,
            RegisterServiceContainer::class,
            RegisterWebhookEvents::class
        ]
    ],
    'update' => 'oat\\taoDelivery\\scripts\\update\\Updater',
    'acl' => [
        ['grant', 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole', ['ext' => 'taoDelivery', 'mod' => 'DeliveryServer']],
        ['grant', TaoRoles::ANONYMOUS, DeliveryServer::class . '@logout'],
        ['grant', TaoRoles::REST_PUBLISHER, ['ext' => 'taoDelivery', 'mod' => 'RestExecution']],
    ],
    'routes' => [
        '/taoDelivery' => 'oat\\taoDelivery\\controller'
    ],
    'constants' => [
        # actions directory
        "DIR_ACTIONS"           => $extpath . "actions" . DIRECTORY_SEPARATOR,

        # views directory
        "DIR_VIEWS"             => $extpath . "views" . DIRECTORY_SEPARATOR,

        # default module name
        'DEFAULT_MODULE_NAME'   => 'DeliveryServer',

        #default action name
        'DEFAULT_ACTION_NAME'   => 'index',

        #BASE PATH: the root path in the file system (usually the document root)
        'BASE_PATH'             => $extpath,

        #BASE URL (usually the domain root)
        'BASE_URL'              => ROOT_URL . 'taoDelivery/',
    ]
];
