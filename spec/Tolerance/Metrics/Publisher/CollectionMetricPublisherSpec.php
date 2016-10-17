<?php

namespace spec\Tolerance\Metrics\Publisher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;

class CollectionMetricPublisherSpec extends ObjectBehavior
{
    function it_is_a_publisher()
    {
        $this->shouldImplement(MetricPublisher::class);
    }

    function it_publishes_to_every_publisher(MetricPublisher $first, MetricPublisher $second)
    {
        $this->beConstructedWith([$first, $second]);

        $metrics = [new Metric('foo', 'bar')];

        $first->publish($metrics)->shouldBeCalled();
        $second->publish($metrics)->shouldBeCalled();

        $this->publish($metrics);
    }

    function it_allows_to_add_a_publisher(MetricPublisher $publisher)
    {
        $this->addPublisher($publisher);

        $metrics = [new Metric('foo', 0)];
        $publisher->publish($metrics)->shouldBeCalled();

        $this->publish($metrics);
    }
}
