<?php

namespace spec\Tolerance\Operation\Runner;

use Tolerance\Operation\Operation;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Waiter\WaiterException;
use Tolerance\Waiter\Strategy\WaitStrategy;
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
            $this->run($operation)->willReturn();

            throw new \RuntimeException('Operation failed');
        });

        $this->run($operation);

        $runner->run($operation)->shouldHaveBeenCalledTimes(2);
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
