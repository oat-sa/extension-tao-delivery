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
 * Copyright (c) 2017  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoDelivery\model\delivery;


use oat\oatbox\service\ConfigurableService;

abstract class AbstractDeliveryService extends ConfigurableService implements DeliveryServiceInterface
{
    /**
     * @var \common_persistence_Persistence
     */
    protected $persistence;

    /**
     * @param $id
     * @param $param
     * @return mixed
     */
    abstract protected function getParameterValue($id, $param);

    public function getParameter($id, $param = '')
    {
        if (!$this->deliveryExists($id)) {
            throw new \ErrorException('Delivery not found [' . $id . ']');
        }

        return $this->parameterExists($id, $param) ? $this->getParameterValue($id, $param) : null;
    }

    public function isExcludedSubject($id, $subject)
    {
        return in_array($subject, $this->getExcludedSubjects($id));
    }

    public function getExcludedSubjects($id)
    {
        return $this->getParameter($id, DeliveryInterface::EXCLUDED_SUBJECTS);
    }

    public function setExcludedSubjects($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::EXCLUDED_SUBJECTS, $val);
    }

    public function getMaxExec($id)
    {
        return $this->getParameter($id, DeliveryInterface::MAX_EXEC);
    }

    public function setMaxExec($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::MAX_EXEC, $val);
    }

    public function getLabel($id)
    {
        return $this->getParameter($id, RDFS_LABEL);
    }

    public function setLabel($id, $val)
    {
        return $this->setParameter($id, RDFS_LABEL, $val);
    }

    public function getPeriodStart($id)
    {
        return $this->getParameter($id, DeliveryInterface::PERIOD_START);
    }

    public function setPeriodStart($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PERIOD_START, $val);
    }

    public function getPeriodEnd($id)
    {
        return $this->getParameter($id, DeliveryInterface::PERIOD_END);
    }

    public function setPeriodEnd($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PERIOD_END, $val);
    }

    public function getCompilationRuntime($id)
    {
        return $this->getParameter($id, DeliveryInterface::ASSEMBLED_DELIVERY_RUNTIME);
    }

    public function setCompilationRuntime($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::ASSEMBLED_DELIVERY_RUNTIME, $val);
    }

    public function getCompilationDirectory($id)
    {
        return $this->getParameter($id, DeliveryInterface::ASSEMBLED_DELIVERY_DIRECTORY);
    }

    public function setCompilationDirectory($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::ASSEMBLED_DELIVERY_DIRECTORY, $val);
    }

    public function getCompilationDate($id)
    {
        return $this->getParameter($id, DeliveryInterface::ASSEMBLED_DELIVERY_TIME);
    }

    public function setCompilationDate($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::ASSEMBLED_DELIVERY_TIME, $val);
    }

    public function getAccessSettings($id)
    {
        return $this->getParameter($id, DeliveryInterface::ACCESS_SETTINGS);
    }

    public function setAccessSettings($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::ACCESS_SETTINGS, $val);
    }

    public function getResultServer($id)
    {
        return $this->getParameter($id, DeliveryInterface::RESULT_SERVER);
    }

    public function setResultServer($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::RESULT_SERVER, $val);
    }
}
