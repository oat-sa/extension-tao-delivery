<?php

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

        $tableName = RdsDeliveryExecutionService::TABLE_NAME;

        if (!$updatedSchema->hasTable($tableName)) {
            $this->addReport(Report::createSuccess('delivery_executions table not present, skipping RDS column'));

            return;
        }

        $table = $updatedSchema->getTable($tableName);

        if (!$table->hasColumn(RdsDeliveryExecutionService::COLUMN_LOCK_DOWN_BROWSER)) {
            $table->addColumn(
                RdsDeliveryExecutionService::COLUMN_LOCK_DOWN_BROWSER,
                'string',
                [
                    'length' => 64,
                    'notnull' => true,
                    'default' => '',
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
        $this->addReport(Report::createSuccess('Down migration not implemented for lock_down_browser column'));
    }
}
