<?php

namespace spec\Tolerance\Bridge\Symfony\Metrics\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tolerance\Metrics\Collector\MetricCollector;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;

class CollectAndPublishMetricsCommandSpec extends ObjectBehavior
{
    function let(MetricCollector $collector, MetricPublisher $publisher)
    {
        $this->beConstructedWith($collector, $publisher);
    }

    function it_is_a_command()
    {
        $this->shouldHaveType(Command::class);
    }

    function it_is_named_tolerance_metrics_collect_and_publish()
    {
        $this->getName()->shouldReturn('tolerance:metrics:collect-and-publish');
    }

    function it_collects_and_publishes_the_metrics(MetricCollector $collector, MetricPublisher $publisher, InputInterface $input, OutputInterface $output)
    {
        $metrics = [new Metric('first', 1), new Metric('bar', 0.5)];

        $collector->collect()->shouldBeCalled()->willReturn($metrics);
        $publisher->publish($metrics)->shouldBeCalled();

        $this->run($input, $output);
    }
}
