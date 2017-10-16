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


/**
 * Class Delivery
 * @package oat\taoDelivery\model\delivery
 */
class Delivery implements DeliveryInterface
{
    /**
     * @var
     */
    private $identifier;

    /**
     * @var DeliveryServiceInterface
     */
    private $service;

    public function __construct($identifier, DeliveryServiceInterface $service)
    {
        $this->identifier = $identifier;
        $this->service = $service;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getMaxExec()
    {
        return $this->service->getMaxExec($this->getIdentifier());
    }

    public function setMaxExec($val)
    {
        $this->service->setMaxExec($this->getIdentifier(), $val);
    }

    public function getLabel()
    {
        return $this->service->getLabel($this->getIdentifier());
    }

    public function setLabel($val)
    {
        $this->service->setLabel($this->getIdentifier(), $val);
    }

    public function getCustomLabel()
    {
        return $this->service->getCustomLabel($this->getIdentifier());
    }

    public function setCustomLabel($val)
    {
        $this->service->setCustomLabel($this->getIdentifier(), $val);
    }

    public function getPeriodEnd()
    {
        return $this->service->getPeriodEnd($this->getIdentifier());
    }

    public function setPeriodEnd($val)
    {
        $this->service->setPeriodEnd($this->getIdentifier(), $val);
    }

    public function getPeriodStart()
    {
        return $this->service->getPeriodStart($this->getIdentifier());
    }

    public function setPeriodStart($val)
    {
        $this->service->setPeriodStart($this->getIdentifier(), $val);
    }

    public function getExcludedSubjects()
    {
        return $this->service->getExcludedSubjects($this->getIdentifier());
    }

    public function setExcludedSubjects($val)
    {
        $this->service->setExcludedSubjects($this->getIdentifier(), $val);
    }

    public function getAccessSettings()
    {
        return $this->service->getAccessSettings($this->getIdentifier());
    }

    public function setAccessSettings(array $val)
    {
        $this->service->setAccessSettings($this->getIdentifier(), $val);
    }

    public function getCompilationDate()
    {
        return $this->service->getCompilationDate($this->getIdentifier());
    }

    public function setCompilationDate($val)
    {
        $this->service->setCompilationDate($this->getIdentifier(), $val);
    }

    public function getCompilationDirectory()
    {
        return $this->service->getCompilationDirectory($this->getIdentifier());
    }

    public function setCompilationDirectory($val)
    {
        $this->service->setCompilationDirectory($this->getIdentifier(), $val);
    }

    public function getCompilationRuntime()
    {
        return $this->service->getCompilationRuntime($this->getIdentifier());
    }

    public function setCompilationRuntime($val)
    {
        $this->service->setCompilationRuntime($this->getIdentifier(), $val);
    }

    public function getAssembledContainer()
    {
        $this->service->getAssembledContainer($this->getIdentifier());
    }

    public function setAssembledContainer($val)
    {
        $this->service->setAssembledContainer($this->getIdentifier(), $val);
    }

    public function getResultServer()
    {
        return $this->service->getResultServer($this->getIdentifier());
    }

    public function setResultServer($val)
    {
        $this->service->setResultServer($this->getIdentifier(), $val);
    }

    public function getDeliveryOrder()
    {
        return $this->service->getDeliveryOrder($this->getIdentifier());
    }

    public function setDeliveryOrder($val)
    {
        $this->service->setDeliveryOrder($this->getIdentifier(), $val);
    }

    public function getDeliveryAssembledOrigin()
    {
        $this->service->getDeliveryAssembledOrigin($this->getIdentifier());
    }

    public function setDeliveryAssembledOrigin($val)
    {
        $this->service->setDeliveryAssembledOrigin($this->getIdentifier(), $val);
    }

    public function setParameter($param = '', $value = '')
    {
        return $this->service->setParameter($this->getIdentifier(), $param = '', $value = '');
    }

    public function setParameters(array $params)
    {
        return $this->service->setParameters($this->getIdentifier(), $params);
    }

    public function isExcludedSubject($subject)
    {
        return $this->service->isExcludedSubject($this->getIdentifier(), $subject);
    }

    public function delete()
    {
        $this->service->delete($this->getIdentifier());
    }
}
