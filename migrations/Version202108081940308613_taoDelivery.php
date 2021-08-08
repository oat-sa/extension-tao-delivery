<?php

declare(strict_types=1);

namespace oat\taoDelivery\migrations;

use common_Exception;
use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\oatbox\service\ServiceNotFoundException;
use oat\tao\model\counter\CounterService;
use oat\tao\model\counter\CounterServiceException;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;
use oat\taoDelivery\scripts\install\RegisterCounters;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202108081940308613_taoDelivery extends AbstractMigration
{

    public function getDescription(): string
    {
        return 'Attach DeliveryExecutionCreated and DeliveryExecutionState Event Counters.';
    }

    /**
     * @param Schema $schema
     * @throws CounterServiceException
     * @throws InvalidServiceManagerException
     * @throws ServiceNotFoundException
     * @throws common_Exception
     */
    public function up(Schema $schema): void
    {
        $registerCounters = new RegisterCounters();
        $this->propagate($registerCounters);
        $registerCounters([]);
    }

    /**
     * @param Schema $schema
     * @throws common_Exception
     * @throws ServiceNotFoundException
     * @throws InvalidServiceManagerException
     * @throws CounterServiceException
     */
    public function down(Schema $schema): void
    {
        $serviceManager = $this->getServiceManager();

        /** @var CounterService $counterService */
        $counterService = $serviceManager->get(CounterService::SERVICE_ID);

        $counterService->detach(DeliveryExecutionCreated::class);
        $counterService->detach(DeliveryExecutionState::class);

        $serviceManager->register(CounterService::SERVICE_ID, $counterService);
    }
}
