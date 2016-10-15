<?php

namespace spec\Tolerance\Metrics\Publisher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;

class LoggerPublisherSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_a_publisher()
    {
        $this->shouldImplement(MetricPublisher::class);
    }

    function it_logs_each_published_metric(LoggerInterface $logger)
    {
        $logger->info(Argument::type('string'))->shouldBeCalledTimes(2);

        $this->publish([new Metric('a', 1), new Metric('b', 2)]);
    }
}
