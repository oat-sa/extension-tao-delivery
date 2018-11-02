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
 * Copyright (c) 2013-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\scripts\install;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use oat\oatbox\extension\AbstractAction;
use oat\taoDelivery\model\execution\rds\RdsDeliveryExecutionService;

class GenerateRdsDeliveryExecutionTable extends AbstractAction
{
    public function __invoke($params)
    {
        /** @var \common_persistence_SqlPersistence $service */
        $persistence = $this->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID)->getPersistenceById("default");

        $this->generateTable($persistence);
    }

    /**
     * @param \common_persistence_SqlPersistence $persistence
     * @throws \common_ext_InstallationException
     */
    public function generateTable(\common_persistence_SqlPersistence $persistence)
    {
        $tableName       = RdsDeliveryExecutionService::TABLE_NAME;
        $schemaManager   = $persistence->getSchemaManager();
        $updatedSchema   = $schemaManager->createSchema();
        $currentSchema   = clone $updatedSchema;
        $isTableExist    = $this->isTableExist($schemaManager);
        $areColumnsExist = $this->areColumnsExist($schemaManager);

        if ($isTableExist && !$areColumnsExist) {
            throw new \common_ext_InstallationException(
                sprintf("'%s' table is already exist, but with the wrong Schema", $tableName)
            );
        }

        if (!$isTableExist) {
            $this->createTable($updatedSchema);
        }

        $queries = $persistence->getPlatForm()->getMigrateSchemaSql($currentSchema, $updatedSchema);

        foreach ($queries as $query) {
            $persistence->exec($query);
        }
    }

    /**
     * Returns that the delivery_executions table is already exist in the database or not
     *
     * @param \common_persistence_sql_SchemaManager $schemaManager
     * @return bool
     */
    private function isTableExist(\common_persistence_sql_SchemaManager $schemaManager)
    {
        return in_array(RdsDeliveryExecutionService::TABLE_NAME, $schemaManager->getTables());
    }

    /**
     * Returns that the delivery_executions table columns are already exist in the table or not
     *
     * @param \common_persistence_sql_SchemaManager $schemaManager
     * @return bool
     */
    private function areColumnsExist(\common_persistence_sql_SchemaManager $schemaManager)
    {
        $columnNames = [
            RdsDeliveryExecutionService::COLUMN_ID,
            RdsDeliveryExecutionService::COLUMN_USER_ID,
            RdsDeliveryExecutionService::COLUMN_DELIVERY_ID,
            RdsDeliveryExecutionService::COLUMN_LABEL,
            RdsDeliveryExecutionService::COLUMN_STATUS,
            RdsDeliveryExecutionService::COLUMN_STARTED_AT,
            RdsDeliveryExecutionService::COLUMN_FINISHED_AT,
        ];

        $tableColumns = array_map(function(Column $column) {
            return $column->getName();
        }, $schemaManager->getColumnNames(RdsDeliveryExecutionService::TABLE_NAME));

        return array_reduce($columnNames, function($areColumnsExist, $column) use ($tableColumns) {
            $areColumnsExist = $areColumnsExist && in_array($column, $tableColumns);

            return $areColumnsExist;
        }, true);
    }

    /**
     * Creates the table in the database for the Delivery
     *
     * @param Schema $schema
     * @return void
     */
    private function createTable(Schema $schema)
    {
        $table = $schema->createTable(RdsDeliveryExecutionService::TABLE_NAME);

        $table->addOption("engine", "InnoDB");
        $table->addOption("charset", "utf8");
        $table->addOption("collate", "utf8_unicode_ci");

        $this->createColumns($table);

        /**
         * Create index for the following methods
         * @see RdsDeliveryExecutionService::getExecutionsByDelivery()
         * @see RdsDeliveryExecutionService::getUserExecutions()
         */
        $this->createIndex($table, [
            RdsDeliveryExecutionService::COLUMN_DELIVERY_ID,
            RdsDeliveryExecutionService::COLUMN_USER_ID,
        ]);

        /**
         * Create index for the following methods
         * @see RdsDeliveryExecutionService::getDeliveryExecutionsByStatus()
         */
        $this->createIndex($table, [
            RdsDeliveryExecutionService::COLUMN_USER_ID,
        ]);
    }

    /**
     * Generates columns
     *
     * @param Table $table
     * @return void
     */
    private function createColumns(Table $table)
    {
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_ID, Type::STRING, ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_DELIVERY_ID, Type::STRING, ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_USER_ID, Type::STRING, ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_LABEL, Type::STRING, ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_STATUS, Type::STRING, ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_STARTED_AT, Type::DATETIME, ["notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_FINISHED_AT, Type::DATETIME, ["notnull" => false]);
        $table->setPrimaryKey([RdsDeliveryExecutionService::COLUMN_ID]);
    }

    /**
     * Generates index for the given columns
     *
     * @param Table $table
     * @param array $columns
     * @return void
     */
    private function createIndex(Table $table, array $columns)
    {
        if (count($columns) > 0) {
            $indexPrefix = "idx_" . RdsDeliveryExecutionService::TABLE_NAME . "_";

            // Index names are limited to 63 characters in PostgreSQL (64 in MySQL)
            $indexName = substr($indexPrefix . implode("_", $columns), 0, 63);

            $table->addIndex($columns, $indexName);
        }
    }
}
