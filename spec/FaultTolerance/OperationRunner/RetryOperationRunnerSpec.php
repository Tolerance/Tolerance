<?php

namespace spec\FaultTolerance\OperationRunner;

use FaultTolerance\Operation;
use FaultTolerance\OperationRunner;
use FaultTolerance\Waiter\WaiterException;
use FaultTolerance\WaitStrategy;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RetryOperationRunnerSpec extends ObjectBehavior
{
    function let(OperationRunner $runner, WaitStrategy $waitStrategy)
    {
        $this->beConstructedWith($runner, $waitStrategy);
    }

    function it_should_be_an_operation_runner()
    {
        $this->shouldHaveType(OperationRunner::class);
    }

    function it_should_retry_to_run_the_operation(OperationRunner $runner, WaitStrategy $waitStrategy, Operation $operation)
    {
        $runner->run($operation)->will(function() use ($operation) {
            $this->run($operation)->willReturn('foo');

            throw new \RuntimeException('Operation failed');
        });

        $this->run($operation);
    }

    function it_should_throw_the_original_exception_if_the_wait_fails(OperationRunner $runner, WaitStrategy $waitStrategy, Operation $operation)
    {
        $runner->run($operation)->will(function() use ($operation) {
            throw new \RuntimeException('Operation failed');
        });

        $waitStrategy->wait()->willThrow(new WaiterException('Retried to many times'));
        $this->shouldThrow(\RuntimeException::class)->duringRun($operation);
    }
}
