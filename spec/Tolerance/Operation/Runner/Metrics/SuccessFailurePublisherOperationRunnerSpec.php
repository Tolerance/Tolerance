<?php

namespace spec\Tolerance\Operation\Runner\Metrics;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;
use Tolerance\Operation\Callback;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Runner\OperationRunner;

class SuccessFailurePublisherOperationRunnerSpec extends ObjectBehavior
{
    function let(OperationRunner $runner, MetricPublisher $publisher)
    {
        $this->beConstructedWith($runner, $publisher, 'my_namespace');
    }

    function it_is_an_operation_runner()
    {
        $this->shouldImplement(OperationRunner::class);
    }

    function it_supports_the_operation_if_supported_by_the_decorated_runner(OperationRunner $runner, Operation $operation)
    {
        $runner->supports($operation)->willReturn(true);
        $this->supports($operation)->shouldReturn(true);
    }

    function it_do_not_supports_the_operation_if_not_supported_by_the_decorated_runner(OperationRunner $runner, Operation $operation)
    {
        $runner->supports($operation)->willReturn(false);
        $this->supports($operation)->shouldReturn(false);
    }

    function it_run_the_operation_and_publish_the_success_metric(OperationRunner $runner, MetricPublisher $publisher, Operation $operation)
    {
        $runner->run($operation)->shouldBeCalled()->willReturn('return');
        $publisher->publish([
            new Metric('my_namespace.success', 1, Metric::TYPE_INCREMENT)
        ])->shouldBeCalled();

        $this->run($operation)->shouldReturn('return');
    }

    function it_publish_a_failure_metric_and_rethrow_an_exception(OperationRunner $runner, MetricPublisher $publisher, Operation $operation)
    {
        $e = new \InvalidArgumentException();
        $runner->run($operation)->willThrow($e);

        $publisher->publish([
            new Metric('my_namespace.failure', 1, Metric::TYPE_INCREMENT)
        ])->shouldBeCalled();

        $this->shouldThrow($e)->during('run', [$operation]);
    }
}
