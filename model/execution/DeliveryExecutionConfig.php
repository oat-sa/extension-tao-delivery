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

namespace oat\taoDelivery\model\execution;

use oat\oatbox\service\ConfigurableService;

class DeliveryExecutionConfig extends ConfigurableService
{
    public const SERVICE_ID = 'taoDelivery/deliveryExecutionConfig';

    public const OPTION_HIDE_HOME_BUTTON = 'hideHomeButton';
    public const OPTION_HIDE_LOGOUT_BUTTON = 'hideLogoutButton';

    /**
     * @return bool
     */
    public function isHomeButtonHidden(): bool
    {
        return self::toBool($this->getOption(self::OPTION_HIDE_HOME_BUTTON, false));
    }

    /**
     * @param bool $hide
     */
    public function setHideHomeButton(bool $hide): void
    {
        $this->setOption(self::OPTION_HIDE_HOME_BUTTON, $hide);
    }

    /**
     * @return bool
     */
    public function isLogoutButtonHidden(): bool
    {
        return self::toBool($this->getOption(self::OPTION_HIDE_LOGOUT_BUTTON, false));
    }

    /**
     * @param bool $hide
     */
    public function setHideLogoutButton(bool $hide): void
    {
        $this->setOption(self::OPTION_HIDE_LOGOUT_BUTTON, $hide);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public static function toBool($value): bool
    {
        if ($value === 'false') {
            $value = false;
        } else {
            $value = (bool) $value;
        }

        return $value;
    }
}
