<?php


namespace oat\taoDelivery\model\Metrics;

use Aws\Result;
use oat\awsTools\AwsClient;
use oat\tao\model\metrics\implementations\abstractMetrics;

class AwsCloudWatchLogRdsLoadMetric extends abstractMetrics implements InfrastructureLoadMetricInterface
{
    /** @var string */
    const OPTION_LOG_GROUP_NAME = 'logGroupName';

    /** @var string */
    const OPTION_LOG_STREAM_NAME = 'logStreamName';

    /**
     * @var AwsClient
     */
    private $awsClient;

    /**
     * @param bool $force
     * @return float
     * @throws \common_Exception
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    public function collect($force = false)
    {
        if ($force || !$metricValue = $this->getPersistence()->get(self::class)) {
            $metricValue = $this->getMetric();
            $this->getPersistence()->set(self::class, $metricValue, $this->getOption(self::OPTION_TTL));
        }

        return $metricValue;
    }

    /**
     * @return mixed
     * @throws MetricConfigurationException
     */
    private function getLogGroupName()
    {
        if (!$this->hasOption(self::OPTION_LOG_GROUP_NAME)) {
            throw new MetricConfigurationException('AWS CloudWatch Logs group not configured.');
        }

        return $this->getOption(self::OPTION_LOG_GROUP_NAME);
    }

    /**
     * @return mixed
     * @throws MetricConfigurationException
     */
    private function getLogStreamName()
    {
        if (!$this->hasOption(self::OPTION_LOG_STREAM_NAME)) {
            throw new MetricConfigurationException('AWS CloudWatch Logs stream not configured.');
        }

        return $this->getOption(self::OPTION_LOG_STREAM_NAME);
    }

    /**
     * @return mixed
     * @throws MetricConfigurationException
     */
    private function getMetric()
    {
        $cloudWatchClient = $this->getAwsClient()->getCloudWatchLogsClient();
        $result = $cloudWatchClient->getLogEvents([
            'limit' => 1,
            'logGroupName' => $this->getLogGroupName(), // REQUIRED
            'logStreamName' => $this->getLogStreamName(), // REQUIRED
            'startFromHead' => false,
        ]);
        $this->logInfo('RDS_METRICS:' . json_encode($result->toArray()));
        return $this->parseMetricValue($result);
    }

    /**
     * Requires lib-generis-aws at least 0.10.0
     * @return AwsClient
     */
    private function getAwsClient()
    {
        if (!$this->awsClient) {
            $this->awsClient = $this->getServiceLocator()->get('generis/awsClient');
        }
        return $this->awsClient;
    }

    /**
     * @param Result $result
     * @return mixed
     */
    private function parseMetricValue(Result $result)
    {
        $default = 0;
        if (!$result->hasKey('events')) {
            return $default;
        }
        $logEvents = $result->get('events');
        $logEvent = $logEvents[0];
        $logMessage = json_decode($logEvent['message'], true);

        return (float) $logMessage['cpuUtilization']['total'];
    }
}