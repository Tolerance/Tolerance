<?php

namespace spec\Tolerance\Metrics\Publisher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;
use Tolerance\Operation\Callback;
use Tolerance\Operation\Runner\OperationRunner;

class DelegatesToOperationRunnerPublisherSpec extends ObjectBehavior
{
    function let(MetricPublisher $decoratedPublisher, OperationRunner $operationRunner)
    {
        $this->beConstructedWith($decoratedPublisher, $operationRunner);
    }

    function it_is_a_metric_publisher()
    {
        $this->shouldImplement(MetricPublisher::class);
    }

    function it_runs_the_publish_via_a_callback_operation(MetricPublisher $decoratedPublisher, OperationRunner $operationRunner)
    {
        $operationRunner->run(Argument::that(function($operation) {
            if (!$operation instanceof Callback) {
                return false;
            }

            $operation->call();

            return true;
        }))->shouldBeCalled();

        $metrics = [
            new Metric('name', 'foo')
        ];

        $decoratedPublisher->publish($metrics)->shouldBeCalled();

        $this->publish($metrics);
    }
}
