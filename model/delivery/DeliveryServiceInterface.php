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


use oat\taoDelivery\model\RuntimeService;
use oat\taoResultServer\models\classes\ResultServerService;

interface DeliveryServiceInterface
{
    const SERVICE_ID = 'taoDelivery\DeliveryService';
    const OPTION_PERSISTENCE = 'persistence';

    /**
     * Checks if delivery exists
     * @param $id
     * @return bool
     */
    public function deliveryExists($id);

    /**
     * Checks if parameter of the delivery exists
     * @param $id
     * @param $param
     * @return mixed
     */
    public function parameterExists($id, $param);

    /**
     * Load parameter from the storage
     * @param string $id
     * @param string $param
     * @return mixed
     */
    public function getParameter($id, $param = '');

    /**
     * @param \core_kernel_classes_Class $deliveryClass
     * @param string $label
     * @return Delivery
     */
    public function createDelivery(\core_kernel_classes_Class $deliveryClass, $label = '');

    /**
     * @param $id Delivery identifier
     * @return string
     */
    public function getLabel($id);

    /**
     * @param $id
     * @param $val
     */
    public function setLabel($id, $val);

    /**
     * @param $id Delivery identifier
     * @return string
     */
    public function getPeriodStart($id);

    /**
     * @param $id
     * @param $val
     */
    public function setPeriodStart($id, $val);

    /**
     * @param $id Delivery identifier
     * @return string
     */
    public function getPeriodEnd($id);

    /**
     * @param $id
     * @param $val
     */
    public function setPeriodEnd($id, $val);

    /**
     * @param $id Delivery identifier
     * @return array
     */
    public function getExcludedSubjects($id);

    /**
     * @param $id
     * @param $val
     */
    public function setExcludedSubjects($id, $val);

    /**
     * @param $id Delivery identifier
     * @param $subject
     * @return bool
     */
    public function isExcludedSubject($id, $subject);

    /**
     * @param $id Delivery identifier
     * @return ResultServerService
     */
    public function getResultServer($id);

    /**
     * @param $id
     * @param $val
     */
    public function setResultServer($id, $val);

    /**
     * @param $id Delivery identifier
     * @return integer
     */
    public function getMaxExec($id);

    /**
     * @param $id
     * @param $val
     */
    public function setMaxExec($id, $val);

    /**
     * @param $id Delivery identifier
     * @return mixed
     */
    public function getAccessSettings($id);

    /**
     * @param $id
     * @param $val
     */
    public function setAccessSettings($id, $val);

    /**
     * @param $id Delivery identifier
     * @return string
     */
    public function getCompilationDate($id);

    /**
     * @param $id
     * @param $val
     */
    public function setCompilationDate($id, $val);

    /**
     * @param $id Delivery identifier
     * @return RuntimeService
     */
    public function getCompilationRuntime($id);

    /**
     * @param $id
     * @param $val
     */
    public function setCompilationRuntime($id, $val);

    /**
     * @param $id Delivery identifier
     * @return \core_kernel_classes_Resource
     */
    public function getCompilationDirectory($id);

    /**
     * @param $id
     * @param $val
     */
    public function setCompilationDirectory($id, $val);

    /**
     * @param $id
     * @param string $param
     * @param string|array $value
     * @return mixed
     */
    public function setParameter($id, $param = '', $value);

    /**
     * @param $id
     * @param array $params ['paramName' => 'paramValue' | ['paramValue1', 'paramValue2']]
     * @return mixed
     */
    public function setParameters($id, array $params);
}
