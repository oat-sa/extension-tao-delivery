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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoDelivery\scripts\tools;

use common_report_Report as Report;
use common_exception_Error as Error;
use oat\oatbox\extension\script\ScriptAction;
use common_ext_ExtensionsManager as ExtensionsManager;
use common_ext_ExtensionException as ExtensionException;
use oat\oatbox\service\exception\InvalidServiceManagerException;

/**
 * Class ConfigureDeliveryExecutionHeader
 * Script allow to control the visibility of the "Home" and "Logout" buttons while executing the delivery.
 *
 * @package oat\taoDelivery\scripts\tools
 */
class ConfigureDeliveryExecutionHeader extends ScriptAction
{
    public const OPTION_HIDE_HOME_BUTTON = 'hideHomeButton';
    public const OPTION_HIDE_LOGOUT_BUTTON = 'hideLogoutButton';

    private const CONFIG_KEY = 'deliveryExecutionHeader';

    /**
     * @return array
     */
    protected function provideOptions()
    {
        return [
            self::OPTION_HIDE_HOME_BUTTON => [
                'description' => 'Allow to hide or show "Home" button.',
                'prefix' => 'h',
                'longPrefix' => self::OPTION_HIDE_HOME_BUTTON,
                'defaultValue' => false,
                'cast' => 'bool',
            ],
            self::OPTION_HIDE_LOGOUT_BUTTON => [
                'description' => 'Allow to hide or show "Logout" button.',
                'prefix' => 'l',
                'longPrefix' => self::OPTION_HIDE_LOGOUT_BUTTON,
                'defaultValue' => false,
                'cast' => 'bool',
            ],
        ];
    }

    /**
     * @return string
     */
    protected function provideDescription()
    {
        return 'Allow to configure buttons visibility for the delivery execution header.';
    }

    /**
     * @throws Error
     * @throws ExtensionException
     * @throws InvalidServiceManagerException
     *
     * @return Report
     */
    protected function run()
    {
        /** @var ExtensionsManager $extensionManager */
        $extensionManager = $this->getServiceManager()->get(ExtensionsManager::SERVICE_ID);
        $extension = $extensionManager->getExtensionById('taoDelivery');

        $config = $extension->getConfig(self::CONFIG_KEY);
        $config[self::OPTION_HIDE_HOME_BUTTON] = $this->getOption(self::OPTION_HIDE_HOME_BUTTON);
        $config[self::OPTION_HIDE_LOGOUT_BUTTON] = $this->getOption(self::OPTION_HIDE_LOGOUT_BUTTON);
        $extension->setConfig(self::CONFIG_KEY, $config);

        return Report::createSuccess(
            'The delivery execution header was successfully configured.'
        );
    }
}
