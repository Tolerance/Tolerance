<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Metrics\Collector\RabbitMq;

use Tolerance\Metrics\Collector\MetricCollector;
use Tolerance\Metrics\Metric;

class RabbitMqCollector implements MetricCollector
{
    /**
     * @var RabbitMqHttpClient
     */
    private $rabbitMqClient;

    /**
     * @var string
     */
    private $vhost;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @param RabbitMqHttpClient $rabbitMqClient
     * @param string             $vhost
     * @param string             $queueName
     */
    public function __construct(RabbitMqHttpClient $rabbitMqClient, $vhost, $queueName)
    {
        $this->rabbitMqClient = $rabbitMqClient;
        $this->vhost = $vhost;
        $this->queueName = $queueName;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $queue = $this->rabbitMqClient->getQueue($this->vhost, $this->queueName);
        $metrics = [
            new Metric('pending', $queue['messages_ready']),
            new Metric('running', $queue['messages_unacknowledged']),
        ];

        if (array_key_exists('message_stats', $queue)) {
            $metrics[] = new Metric('publish_rate', $queue['message_stats']['publish_details']['rate']);
            $metrics[] = new Metric('deliver_rate', $queue['message_stats']['deliver_get_details']['rate']);
        }

        return $metrics;
    }
}
