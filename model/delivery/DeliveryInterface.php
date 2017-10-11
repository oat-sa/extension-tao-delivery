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
    const ASSEMBLED_DELIVERY = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDelivery';
    const EXCLUDED_SUBJECTS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects';
    const RESULT_SERVER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer';
    const MAX_EXEC = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec';
    const PERIOD_START = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart';
    const PERIOD_END = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd';
    const ACCESS_SETTINGS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AccessSettings';
    const ASSEMBLED_DELIVERY_TIME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryCompilationTime';
    const ASSEMBLED_DELIVERY_RUNTIME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryRuntime';
    const ASSEMBLED_DELIVERY_DIRECTORY = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryCompilationDirectory';

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
    public function setAccessSettings($val);

    /**
     * @return string
     */
    public function getCompilationDate();

    /**
     * @param $val
     */
    public function setCompilationDate($val);

    /**
     * @return RuntimeService
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
}
