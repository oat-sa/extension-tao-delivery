<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoDelivery\model\execution;

interface LockDownBrowserAwareDeliveryExecutionInterface
{
    /**
     * @return string Provider id (e.g. LockDownBrowser::PROVIDER_KIOSKED) or empty when none
     */
    public function getLockDownBrowser(): string;
}
