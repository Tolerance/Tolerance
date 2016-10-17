<?php

namespace spec\Tolerance\Metrics\Collector;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Metrics\Collector\MetricCollector;
use Tolerance\Metrics\Metric;

class CollectionMetricCollectorSpec extends ObjectBehavior
{
    function it_is_a_metric_collector()
    {
        $this->shouldImplement(MetricCollector::class);
    }

    function it_collects_and_merge_metrics_from_every_collector(MetricCollector $first, MetricCollector $second)
    {
        $this->beConstructedWith([$first, $second]);

        $m1 = new Metric('first', 1);
        $m2 = new Metric('second', 2);
        $m3 = new Metric('third', 3);

        $first->collect()->willReturn([$m1]);
        $second->collect()->willReturn([$m2, $m3]);

        $this->collect()->shouldBeLike([$m1, $m2, $m3]);
    }

    function it_allows_to_add_a_collector(MetricCollector $first, MetricCollector $added)
    {
        $this->beConstructedWith([$first]);
        $this->addCollector($added);

        $m1 = new Metric('first', 1);
        $m2 = new Metric('second', 2);

        $first->collect()->willReturn([$m1]);
        $added->collect()->willReturn([$m2]);

        $this->collect()->shouldBeLike([$m1, $m2]);
    }
}
