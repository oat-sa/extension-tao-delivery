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

    public function getAllParams()
    {
        return [
            DeliveryInterface::PROPERTY_ACCESS_SETTINGS,
            DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_DIRECTORY,
            DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY,
            DeliveryInterface::PROPERTY_DISPLAY_ORDER,
            DeliveryInterface::PROPERTY_RESULT_SERVER,
            DeliveryInterface::PROPERTY_PERIOD_END,
            DeliveryInterface::PROPERTY_PERIOD_START,
            DeliveryInterface::PROPERTY_MAX_EXEC,
            DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_TIME,
            DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_RUNTIME,
            DeliveryInterface::PROPERTY_EXCLUDED_SUBJECTS,
            DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_ORIGIN,
            DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_CONTAINER,
            DeliveryInterface::PROPERTY_CUSTOM_LABEL,
        ];
    }

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
        return $this->getParameter($id, DeliveryInterface::PROPERTY_EXCLUDED_SUBJECTS);
    }

    public function setExcludedSubjects($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_EXCLUDED_SUBJECTS, $val);
    }

    public function getMaxExec($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_MAX_EXEC);
    }

    public function setMaxExec($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_MAX_EXEC, $val);
    }

    public function getLabel($id)
    {
        return $this->getParameter($id, RDFS_LABEL);
    }

    public function setLabel($id, $val)
    {
        return $this->setParameter($id, RDFS_LABEL, $val);
    }

    public function getCustomLabel($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_CUSTOM_LABEL);
    }

    public function setCustomLabel($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_CUSTOM_LABEL, $val);
    }

    public function getDeliveryAssembledOrigin($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_ORIGIN);
    }

    public function setDeliveryAssembledOrigin($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_ORIGIN, $val);
    }

    public function getPeriodStart($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_PERIOD_START);
    }

    public function setPeriodStart($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_PERIOD_START, $val);
    }

    public function getPeriodEnd($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_PERIOD_END);
    }

    public function setPeriodEnd($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_PERIOD_END, $val);
    }

    public function getCompilationRuntime($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_RUNTIME);
    }

    public function setCompilationRuntime($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_RUNTIME, $val);
    }

    public function getCompilationDirectory($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_DIRECTORY);
    }

    public function setCompilationDirectory($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_DIRECTORY, $val);
    }

    public function getCompilationDate($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_TIME);
    }

    public function setCompilationDate($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_TIME, $val);
    }

    public function getAssembledContainer($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_CONTAINER);
    }

    public function setAssembledContainer($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_ASSEMBLED_DELIVERY_CONTAINER, $val);
    }

    public function getAccessSettings($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_ACCESS_SETTINGS);
    }

    public function setAccessSettings($id, array $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_ACCESS_SETTINGS, $val);
    }

    public function getResultServer($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_RESULT_SERVER);
    }

    public function setResultServer($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_RESULT_SERVER, $val);
    }

    public function getDeliveryOrder($id)
    {
        return $this->getParameter($id, DeliveryInterface::PROPERTY_DISPLAY_ORDER);
    }

    public function setDeliveryOrder($id, $val)
    {
        return $this->setParameter($id, DeliveryInterface::PROPERTY_DISPLAY_ORDER, $val);
    }

    public function delete($id)
    {
        foreach ($this->getAllParams() as $param) {
            $this->deleteParameter($id, $param);
        }
    }
}
