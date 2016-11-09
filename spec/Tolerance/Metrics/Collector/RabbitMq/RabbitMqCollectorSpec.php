<?php

namespace spec\Tolerance\Metrics\Collector\RabbitMq;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Metrics\Collector\MetricCollector;
use Tolerance\Metrics\Collector\RabbitMq\RabbitMqHttpClient;
use Tolerance\Metrics\Metric;

class RabbitMqCollectorSpec extends ObjectBehavior
{
    function let(RabbitMqHttpClient $client)
    {
        $this->beConstructedWith($client, 'vhost', 'queue');
    }

    function it_is_a_metric_collector()
    {
        $this->shouldImplement(MetricCollector::class);
    }

    function it_collects_the_pending_and_running_messages(RabbitMqHttpClient $client)
    {
        $client->getQueue('vhost', 'queue')->willReturn([
            'messages_ready' => 1,
            'messages_unacknowledged' => 10,
        ]);

        $this->collect()->shouldBeLike([
            new Metric('pending', 1),
            new Metric('running', 10),
        ]);
    }

    function it_collects_the_rates_from_stats_if_available(RabbitMqHttpClient $client)
    {
        $client->getQueue('vhost', 'queue')->willReturn([
            'messages_ready' => 1,
            'messages_unacknowledged' => 10,
            'message_stats' => [
                'publish_details' => [
                    'rate' => 0.12,
                ],
                'deliver_get_details' => [
                    'rate' => 12.34,
                ],
            ],
        ]);

        $this->collect()->shouldBeLike([
            new Metric('pending', 1),
            new Metric('running', 10),
            new Metric('publish_rate', 0.12),
            new Metric('deliver_rate', 12.34),
        ]);
    }
}
