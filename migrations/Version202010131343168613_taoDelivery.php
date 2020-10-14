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

namespace oat\taoDelivery\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoDelivery\scripts\tools\ConfigureDeliveryExecutionHeader;

/**
 * Class Version202010131343168613_taoDelivery
 *
 * @package oat\taoDelivery\migrations
 */
final class Version202010131343168613_taoDelivery extends AbstractMigration
{
    private const CONFIG_KEY = 'deliveryExecutionHeader';

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Init default delivery execution header configuration.';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->propagate(new ConfigureDeliveryExecutionHeader())([]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
