<?php

namespace oat\taoDelivery\scripts\install;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
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
        $isTableExist    = $this->isTableExist($schemaManager, $tableName);
        $areColumnsExist = $this->areColumnsExist($schemaManager, $tableName, [
            RdsDeliveryExecutionService::COLUMN_ID,
            RdsDeliveryExecutionService::COLUMN_USER_ID,
            RdsDeliveryExecutionService::COLUMN_DELIVERY_ID,
            RdsDeliveryExecutionService::COLUMN_LABEL,
            RdsDeliveryExecutionService::COLUMN_STATUS,
            RdsDeliveryExecutionService::COLUMN_STARTED_AT,
            RdsDeliveryExecutionService::COLUMN_FINISHED_AT,
        ]);

        if ($isTableExist) {
            if ($areColumnsExist) {
                \common_Logger::i(sprintf("'%s' table is already exist with the correct Schema. Skipping...", $tableName));
            } else {
                throw new \common_ext_InstallationException(
                    sprintf("'%s' table is already exist, but with the wrong Schema", $tableName)
                );
            }
        } else {
            \common_Logger::i(sprintf("'%s' table is not exists. Creating...", $tableName));

            $this->createTable($updatedSchema);

            $queries = $persistence->getPlatForm()->getMigrateSchemaSql($currentSchema, $updatedSchema);

            foreach ($queries as $query) {
                $persistence->exec($query);
            }
        }
    }

    /**
     * Returns that the given table is already exist in the database or not
     *
     * @param \common_persistence_sql_SchemaManager $schemaManager
     * @param $tableName
     * @return bool
     */
    private function isTableExist(\common_persistence_sql_SchemaManager $schemaManager, $tableName)
    {
        return in_array($tableName, $schemaManager->getTables());
    }

    /**
     * Returns that the given columns are already exist in the table or not
     *
     * @param \common_persistence_sql_SchemaManager $schemaManager
     * @param $tableName
     * @param $columnNames
     * @return bool
     */
    private function areColumnsExist(\common_persistence_sql_SchemaManager $schemaManager, $tableName, $columnNames)
    {
        $tableColumns = array_map(function(Column $column) {
            return $column->getName();
        }, $schemaManager->getColumnNames($tableName));

        return array_reduce($columnNames, function($areColumnsExist, $column) use ($tableColumns) {
            $areColumnsExist = $areColumnsExist && in_array($column, $tableColumns);

            return $areColumnsExist;
        }, true);
    }

    /**
     * Creates the table in the database for the Delivery
     *
     * @param Schema $schema
     */
    private function createTable(Schema $schema)
    {
        $table = $schema->createTable(RdsDeliveryExecutionService::TABLE_NAME);

        $table->addOption("engine", "MyISAM");
        $table->addOption("charset", "utf8");
        $table->addOption("collate", "utf8_unicode_ci");

        $this->createColumns($table);
    }

    private function createColumns(Table $table)
    {
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_ID, "string", ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_DELIVERY_ID, "string", ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_USER_ID, "string", ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_LABEL, "string", ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_STATUS, "string", ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_STARTED_AT, "string", ["length" => 255, "notnull" => true]);
        $table->addColumn(RdsDeliveryExecutionService::COLUMN_FINISHED_AT, "string", ["length" => 255, "notnull" => false]);
        $table->setPrimaryKey([RdsDeliveryExecutionService::COLUMN_ID]);
    }
}