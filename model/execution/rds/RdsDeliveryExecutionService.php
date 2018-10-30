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
 */
namespace oat\taoDelivery\model\execution\rds;

use common_persistence_sql_pdo_mysql_Driver;
use common_persistence_SqlPersistence;
use core_kernel_classes_Resource;
use Doctrine\DBAL\Query\QueryBuilder;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\Delete\DeliveryExecutionDeleteRequest;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\model\execution\Monitoring;

/**
 * RDS implementation of the Delivery Execution Service
 *
 * @author Péter Halász <peter@taotesting.com>
 */
class RdsDeliveryExecutionService extends ConfigurableService implements Monitoring
{
    const ID_PREFIX          = "rds_de_";
    const TABLE_NAME         = "delivery_executions";
    const COLUMN_ID          = "id";
    const COLUMN_DELIVERY_ID = "delivery_id";
    const COLUMN_USER_ID     = "user_id";
    const COLUMN_STATUS      = "status";
    const COLUMN_FINISHED_AT = "finished_at";
    const COLUMN_STARTED_AT  = "started_at";
    const COLUMN_LABEL       = "label";

    /**
     * @param DeliveryExecutionDeleteRequest $request
     * @throws \Exception
     * @return bool
     */
    public function deleteDeliveryExecutionData(DeliveryExecutionDeleteRequest $request)
    {
        $query = $this->getQueryBuilder()
            ->delete(self::TABLE_NAME)
            ->where(self::COLUMN_ID . " = :id")
            ->setParameter("id", $request->getDeliveryExecution()->getIdentifier())
        ;

        return $query->execute();
    }

    /**
     * Returns the delivery executions for a compiled directory
     *
     * @param core_kernel_classes_Resource $compiled
     * @return DeliveryExecution[]
     */
    public function getExecutionsByDelivery(core_kernel_classes_Resource $compiled)
    {
        $query = $this->getQueryBuilder()
            ->select("*")
            ->from(self::TABLE_NAME)
            ->where(self::COLUMN_DELIVERY_ID . " = :deliveryId")
            ->setParameter("deliveryId", $compiled->getUri())
        ;

        return array_map(function($row) {
            return $this->parseQueryResult($row);
        }, $query->execute()->fetchAll());
    }

    /**
     * Returns the executions the user has of a specified assembly
     *
     * @param core_kernel_classes_Resource $assembly
     * @param string $userUri
     * @return DeliveryExecution[]
     */
    public function getUserExecutions(core_kernel_classes_Resource $assembly, $userUri)
    {
        $query = $this->getQueryBuilder()
            ->select("*")
            ->from(self::TABLE_NAME)
            ->where(self::COLUMN_DELIVERY_ID . " = :deliveryId")
            ->andWhere(self::COLUMN_USER_ID . " = :userId")
            ->setParameter("deliveryId", $assembly->getUri())
            ->setParameter("userId", $userUri)
        ;

        return array_map(function($row) {
            return $this->parseQueryResult($row);
        }, $query->execute()->fetchAll());
    }

    /**
     * Returns all Delivery Executions of a User with a specific status
     *
     * @param string $userUri
     * @param string $status
     * @return array
     */
    public function getDeliveryExecutionsByStatus($userUri, $status)
    {
        $query = $this->getQueryBuilder()
            ->select("*")
            ->from(self::TABLE_NAME)
            ->where(self::COLUMN_USER_ID . " = :userId")
            ->andWhere(self::COLUMN_STATUS . " = :status")
            ->setParameter("userId", $userUri)
            ->setParameter("status", $status)
        ;

        return array_map(function($row) {
            return $this->parseQueryResult($row);
        }, $query->execute()->fetchAll());
    }

    /**
     * @param $label
     * @param $deliveryId
     * @param $userId
     * @param $status
     * @return DeliveryExecution
     * @throws \common_exception_Error
     */
    public function spawnDeliveryExecution($label, $deliveryId, $userId, $status)
    {
        $deliveryExecutionId = self::ID_PREFIX . $this->getNewUri();

        $this->getPersistence()->insert(self::TABLE_NAME, [
            self::COLUMN_ID => $deliveryExecutionId,
            self::COLUMN_LABEL => $label,
            self::COLUMN_DELIVERY_ID => $deliveryId,
            self::COLUMN_USER_ID => $userId,
            self::COLUMN_STATUS => $status,
            self::COLUMN_STARTED_AT => $this->getCurrentDateTime(),
        ]);

        return $this->getDeliveryExecution($deliveryExecutionId);
    }

    /**
     * Generate a new delivery execution
     *
     * @deprecated
     * @param core_kernel_classes_Resource $assembly
     * @param User $user
     * @return DeliveryExecution the delivery execution
     * @throws \common_exception_Error
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $user)
    {
        return $this->spawnDeliveryExecution(
            $assembly->getLabel(),
            $assembly->getUri(),
            $user,
            DeliveryExecution::STATE_ACTIVE
        );
    }

    /**
     * Returns the delivery execution instance associated to the implementation
     *
     * @param string $identifier
     * @return DeliveryExecution
     * @throws \common_exception_Error
     */
    public function getDeliveryExecution($identifier)
    {
        $query = $this->getQueryBuilder()
            ->select("*")
            ->from(self::TABLE_NAME)
            ->where(self::COLUMN_ID . " = :id")
            ->setParameter("id", $identifier)
        ;

        $result = $query->execute()->fetch();

        if (!$result) {
            $result = [];
        }

        return $this->parseQueryResult($result);
    }

    /**
     * Updates the state of the given deliveryexecution
     *
     * @param string $identifier                         the ID of the delivery execution
     * @param core_kernel_classes_Resource $fromState    the original state
     * @param string $toState                            the desired state
     * @return bool                                      true if the update went well, false otherwise
     */
    public function updateDeliveryExecutionState($identifier, $fromState, $toState)
    {
        try {
            if ($fromState === $toState) {
                // do nothing, when the state didn't change
                return true;
            }

            $query = $this->getQueryBuilder()
                ->update(self::TABLE_NAME)
                ->set(self::COLUMN_STATUS, ":status")
                ->where(self::COLUMN_ID . " = :id")
                ->setParameter("status", $toState)
                ->setParameter("id", $identifier)
            ;

            if ($toState === DeliveryExecutionInterface::STATE_FINISHED) {
                $query
                    ->set(self::COLUMN_FINISHED_AT, ":finishedAt")
                    ->setParameter("finishedAt", $this->getCurrentDateTime())
                ;
            }

            return $query->execute() === 1;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Returns the default SQL persistence
     *
     * @return common_persistence_SqlPersistence
     */
    public function getPersistence()
    {
        return $this->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID)->getPersistenceById("default");
    }

    /**
     * Returns a new Uri
     * (moved to a separated function to be able to mock it during unit tests)
     *
     * @return string
     */
    protected function getNewUri()
    {
        return \common_Utils::getNewUri();
    }

    /**
     * Returns the QueryBuilder
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        /**@var common_persistence_sql_pdo_mysql_Driver $driver */
        return $this->getPersistence()->getPlatform()->getQueryBuilder();
    }

    /**
     * Parses the query result array and constructs a new DeliveryExecution object from it
     *
     * @param array $result
     * @return DeliveryExecution
     * @throws \common_exception_Error
     */
    private function parseQueryResult($result = [])
    {
        $rdsDeliveryExecution = new RdsDeliveryExecution($this);

        if (array_key_exists(self::COLUMN_ID, $result)) {
            $rdsDeliveryExecution->setIdentifier($result[self::COLUMN_ID]);
        }

        if (array_key_exists(self::COLUMN_LABEL, $result)) {
            $rdsDeliveryExecution->setLabel($result[self::COLUMN_LABEL]);
        }

        if (array_key_exists(self::COLUMN_DELIVERY_ID, $result)) {
            $rdsDeliveryExecution->setDelivery($result[self::COLUMN_DELIVERY_ID]);
        }

        if (array_key_exists(self::COLUMN_STATUS, $result)) {
            $rdsDeliveryExecution->setState($result[self::COLUMN_STATUS]);
        }

        if (array_key_exists(self::COLUMN_USER_ID, $result)) {
            $rdsDeliveryExecution->setUserIdentifier($result[self::COLUMN_USER_ID]);
        }

        if (array_key_exists(self::COLUMN_STARTED_AT, $result)) {
            $rdsDeliveryExecution->setStartTime(new \DateTime($result[self::COLUMN_STARTED_AT]));
        }

        if (array_key_exists(self::COLUMN_FINISHED_AT, $result)) {
            $rdsDeliveryExecution->setFinishTime(new \DateTime($result[self::COLUMN_FINISHED_AT]));
        }

        return $this->propagate(new DeliveryExecution($rdsDeliveryExecution));
    }

    /**
     * Returns the current DateTime in a predefined format
     *
     * @return string
     */
    private function getCurrentDateTime()
    {
        return $this->getPersistence()->getPlatform()->getNowExpression();
    }
}
