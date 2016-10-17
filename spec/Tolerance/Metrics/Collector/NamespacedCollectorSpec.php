<?php

namespace spec\Tolerance\Metrics\Collector;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Metrics\Collector\MetricCollector;
use Tolerance\Metrics\Metric;

class NamespacedCollectorSpec extends ObjectBehavior
{
    function let(MetricCollector $collector)
    {
        $this->beConstructedWith($collector, 'my_namespace');
    }

    function it_is_a_metric_collector()
    {
        $this->shouldImplement(MetricCollector::class);
    }

    function it_prefixes_every_metric_with_the_namespace(MetricCollector $collector)
    {
        $collector->collect()->willReturn([
            new Metric('foo', 1),
            new Metric('bar.baz', 'string'),
        ]);

        $this->collect()->shouldBeLike([
            new Metric('my_namespace.foo', 1),
            new Metric('my_namespace.bar.baz', 'string'),
        ]);
    }
}
