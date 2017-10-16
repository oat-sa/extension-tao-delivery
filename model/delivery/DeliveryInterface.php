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

interface DeliveryInterface
{
    const PROPERTY_ASSEMBLED_DELIVERY = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDelivery';
    const PROPERTY_EXCLUDED_SUBJECTS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects';
    const PROPERTY_RESULT_SERVER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer';
    const PROPERTY_MAX_EXEC = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec';
    const PROPERTY_PERIOD_START = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart';
    const PROPERTY_PERIOD_END = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd';
    const PROPERTY_ACCESS_SETTINGS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AccessSettings';
    const PROPERTY_ASSEMBLED_DELIVERY_TIME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryCompilationTime';
    const PROPERTY_ASSEMBLED_DELIVERY_RUNTIME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryRuntime';
    const PROPERTY_ASSEMBLED_DELIVERY_DIRECTORY = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryCompilationDirectory';
    const PROPERTY_ASSEMBLED_DELIVERY_ORIGIN = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryOrigin';
    const PROPERTY_ASSEMBLED_DELIVERY_CONTAINER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryContainer';
    const PROPERTY_DISPLAY_ORDER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DisplayOrder';
    const PROPERTY_CUSTOM_LABEL = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CustomLabel';

    const DELIVERY_GUEST_ACCESS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#GuestAccess';

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param $val
     */
    public function setLabel($val);

    /**
     * @return string
     */
    public function getCustomLabel();

    /**
     * @param $val
     */
    public function setCustomLabel($val);

    /**
     * @return string
     */
    public function getPeriodStart();

    /**
     * @param $val
     */
    public function setPeriodStart($val);

    /**
     * @return string
     */
    public function getPeriodEnd();

    /**
     * @param $val
     */
    public function setPeriodEnd($val);

    /**
     * @return array
     */
    public function getExcludedSubjects();

    /**
     * @param $val
     */
    public function setExcludedSubjects($val);

    /**
     * @param $subject
     * @return bool
     */
    public function isExcludedSubject($subject);

    /**
     * @return ResultServerService
     */
    public function getResultServer();

    /**
     * @param $val
     */
    public function setResultServer($val);

    /**
     * @return integer
     */
    public function getMaxExec();

    /**
     * @param $val
     */
    public function setMaxExec($val);

    /**
     * @return mixed
     */
    public function getAccessSettings();

    /**
     * @param $val
     */
    public function setAccessSettings(array $val);

    /**
     * @return string
     */
    public function getCompilationDate();

    /**
     * @param $val
     */
    public function setCompilationDate($val);

    /**
     * @return string
     */
    public function getCompilationRuntime();

    /**
     * @param $val
     */
    public function setCompilationRuntime($val);

    /**
     * @return \core_kernel_classes_Resource
     */
    public function getCompilationDirectory();

    /**
     * @param $val
     */
    public function setCompilationDirectory($val);

    /**
     * @return mixed
     */
    public function getAssembledContainer();

    /**
     * @param $val
     * @return mixed
     */
    public function setAssembledContainer($val);

    /**
     * @return mixed
     */
    public function getDeliveryOrder();

    /**
     * @param $val
     * @return mixed
     */
    public function setDeliveryOrder($val);

    /**
     * @return mixed
     */
    public function getDeliveryAssembledOrigin();

    /**
     * @param $val
     * @return mixed
     */
    public function setDeliveryAssembledOrigin($val);

    /**
     * @param string $param
     * @param string|array $value
     * @return mixed
     */
    public function setParameter($param = '', $value = '');

    /**
     * @param array $params
     * @return mixed
     */
    public function setParameters(array $params);

    /**
     * @return mixed
     */
    public function delete();
}
