<?php

namespace spec\Tolerance\Metrics\Publisher;

use Beberlei\Metrics\Collector\Collector;
use PhpSpec\ObjectBehavior;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;

class BeberleiMetricsAdapterPublisherSpec extends ObjectBehavior
{
    function let(Collector $beberleiCollector)
    {
        $this->beConstructedWith($beberleiCollector);

        $beberleiCollector->flush()->willReturn(null);
    }

    function it_is_a_metric_publisher()
    {
        $this->shouldImplement(MetricPublisher::class);
    }

    function it_measure_a_metric_by_default(Collector $beberleiCollector)
    {
        $beberleiCollector->measure('foo', 1)->shouldBeCalled();

        $this->publish([new Metric('foo', 1)]);
    }

    function it_supports_many_metrics(Collector $beberleiCollector)
    {
        $beberleiCollector->measure('foo', 1)->shouldBeCalled();
        $beberleiCollector->measure('bar', 'baz')->shouldBeCalled();

        $this->publish([
            new Metric('foo', 1),
            new Metric('bar', 'baz'),
        ]);
    }

    function it_uses_the_correct_method_based_on_the_metrics_type(Collector $beberleiCollector)
    {
        $beberleiCollector->increment('foo')->shouldBeCalled();
        $beberleiCollector->decrement('bar')->shouldBeCalled();
        $beberleiCollector->timing('baz', 0.5)->shouldBeCalled();

        $this->publish([
            new Metric('foo', 1, Metric::TYPE_INCREMENT),
            new Metric('bar', 'baz', Metric::TYPE_DECREMENT),
            new Metric('baz', 0.5, Metric::TYPE_TIMING),
        ]);
    }

    function it_auto_flushes_by_default(Collector $beberleiCollector)
    {
        $this->publish([]);

        $beberleiCollector->flush()->shouldBeCalled();
    }

    function it_will_not_auto_flush_if_flagged_as_not(Collector $beberleiCollector)
    {
        $this->beConstructedWith($beberleiCollector, false);

        $this->publish([]);

        $beberleiCollector->flush()->shouldNotBeCalled();
    }
}
