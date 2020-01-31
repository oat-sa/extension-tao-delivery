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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA
 */

namespace oat\taoDelivery\model\Metrics;

use DateInterval;
use DateTime;
use oat\awsTools\AwsClient;
use oat\tao\model\metrics\implementations\abstractMetrics;

class AwsLoadMetric extends abstractMetrics implements InfrastructureLoadMetricInterface
{
    private $awsClient;

    const OPTION_PERIOD = 'period';
    const OPTION_NAMESPACE = 'namespace';
    const OPTION_DIMENSIONS = 'dimensions';

    /**
     * in seconds
     * @return int
     */
    public function getPeriod()
    {
        return $this->getOption(self::OPTION_PERIOD);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->getOption(self::OPTION_NAMESPACE);
    }

    /**
     * @return array
     */
    public function getDimensions()
    {
        return $this->getOption(self::OPTION_DIMENSIONS);
    }

    public function collect($force = false)
    {
        $active = $this->getPersistence()->get(self::class);
        if (!$active || $force) {
            $active = $this->getMetric();
            $this->getPersistence()->set(self::class, $active, $this->getOption(self::OPTION_TTL));
        }
        return $active;
    }

    private function getMetric()
    {
        $cloudWatchClient = $this->getAwsClient()->getCloudWatchClient();

        $period = $this->getPeriod();
        $interval = new DateInterval('PT' . $period . 'S');
        $since = (new DateTime())->sub($interval);

        $result = $cloudWatchClient->getMetricStatistics([
            'Namespace' => $this->getNamespace(),
            'MetricName' => 'CPUUtilization',
            'StartTime' => $since,
            'EndTime' => new DateTime(),
            'Period' => $period,
            'Statistics' => ['Average', 'Maximum'],
            'Dimensions' => $this->getDimensions(),
        ]);
        $this->logInfo('RDS_METRICS:' . json_encode($result->toArray()));
        $data = $result->get('Datapoints');
        $v = array_shift($data);
        return $v['Average'];
    }

    /**
     * Requires lib-generis-aws at least 0.9.3
     * @return AwsClient
     */
    public function getAwsClient()
    {
        if (!$this->awsClient) {
            $this->awsClient = $this->getServiceLocator()->get('generis/awsClient');
        }
        return $this->awsClient;
    }
}
