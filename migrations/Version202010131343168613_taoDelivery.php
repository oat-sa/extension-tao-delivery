<?php

declare(strict_types=1);

namespace oat\taoDelivery\migrations;

use Doctrine\DBAL\Schema\Schema;
use common_exception_Error as Error;
use common_ext_Extension as Extension;
use common_ext_ExtensionsManager as ExtensionsManager;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use common_ext_ExtensionException as ExtensionException;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\taoDelivery\scripts\tools\ConfigureDeliveryExecutionHeader;

/**
 * Auto-generated Migration: Please modify to your needs!
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
        $this->propagate(new ConfigureDeliveryExecutionHeader())();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
