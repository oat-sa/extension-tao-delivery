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
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoDelivery\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\reporting\Report;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecutionService;

/**
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202610091630002236_taoDelivery extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add lock_down_browser column to delivery_executions RDS table';
    }

    public function up(Schema $schema): void
    {
        $persistence = $this->getServiceManager()
            ->get(\common_persistence_Manager::SERVICE_ID)
            ->getPersistenceById('default');

        $schemaManager = $persistence->getSchemaManager();
        $updatedSchema = $schemaManager->createSchema();
        $currentSchema = clone $updatedSchema;

        if (!$updatedSchema->hasTable(RdsDeliveryExecutionService::TABLE_NAME)) {
            $this->addReport(Report::createSuccess('delivery_executions table not present, skipping RDS column'));

            return;
        }

        $table = $updatedSchema->getTable(RdsDeliveryExecutionService::TABLE_NAME);

        if (!$table->hasColumn(RdsDeliveryExecutionService::COLUMN_LOCK_DOWN_BROWSER)) {
            $table->addColumn(
                RdsDeliveryExecutionService::COLUMN_LOCK_DOWN_BROWSER,
                'string',
                [
                    'length' => 64,
                    'notnull' => false,
                ]
            );
        }

        $queries = $persistence->getPlatForm()->getMigrateSchemaSql($currentSchema, $updatedSchema);

        foreach ($queries as $query) {
            $persistence->exec($query);
        }

        $this->addReport(Report::createSuccess('Added lock_down_browser column when applicable'));
    }

    public function down(Schema $schema): void
    {
        $persistence = $this->getServiceManager()
            ->get(\common_persistence_Manager::SERVICE_ID)
            ->getPersistenceById('default');

        $schemaManager = $persistence->getSchemaManager();
        $updatedSchema = $schemaManager->createSchema();
        $currentSchema = clone $updatedSchema;

        if (!$updatedSchema->hasTable(RdsDeliveryExecutionService::TABLE_NAME)) {
            $this->addReport(Report::createSuccess('delivery_executions table not present, skipping RDS column drop'));

            return;
        }

        $table = $updatedSchema->getTable(RdsDeliveryExecutionService::TABLE_NAME);

        if ($table->hasColumn(RdsDeliveryExecutionService::COLUMN_LOCK_DOWN_BROWSER)) {
            $table->dropColumn(RdsDeliveryExecutionService::COLUMN_LOCK_DOWN_BROWSER);
        }

        $queries = $persistence->getPlatForm()->getMigrateSchemaSql($currentSchema, $updatedSchema);

        foreach ($queries as $query) {
            $persistence->exec($query);
        }

        $this->addReport(Report::createSuccess('Dropped lock_down_browser column when applicable'));
    }
}
