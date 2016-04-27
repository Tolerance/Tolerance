<?php

namespace spec\Tolerance\Operation\Runner;

use Tolerance\Operation\ExceptionCatcher\ExceptionCatcherVoter;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Waiter\WaiterException;
use Tolerance\Waiter\Waiter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RetryOperationRunnerSpec extends ObjectBehavior
{
    function let(OperationRunner $runner, Waiter $waitStrategy)
    {
        $this->beConstructedWith($runner, $waitStrategy);
    }

    function it_should_be_an_operation_runner()
    {
        $this->shouldHaveType(OperationRunner::class);
    }

    function it_should_retry_to_run_the_operation(OperationRunner $runner, Operation $operation)
    {
        $runner->run($operation)->will(function() use ($operation) {
            $this->run($operation)->willReturn(new \stdClass());

            throw new \RuntimeException('Operation failed');
        });

        $runner->run($operation)->shouldBeCalledTimes(2);

        $this->run($operation)->shouldBeLike(new \stdClass());
    }

    function it_should_throw_the_original_exception_if_the_wait_fails(OperationRunner $runner, Waiter $waitStrategy, Operation $operation)
    {
        $runner->run($operation)->will(function() use ($operation) {
            throw new \RuntimeException('Operation failed');
        });

        $waitStrategy->wait()->willThrow(new WaiterException('Retried to many times'));
        $this->shouldThrow(\RuntimeException::class)->duringRun($operation);
    }

    function it_should_not_retry_if_the_optional_catcher_voter_says_no(OperationRunner $runner, Operation $operation, Waiter $waitStrategy, ExceptionCatcherVoter $exceptionCatcherVoter)
    {
        $this->beConstructedWith($runner, $waitStrategy, $exceptionCatcherVoter);

        $exceptionCatcherVoter->shouldCatch(Argument::any())->willReturn(false);
        $runner->run($operation)->will(function() use ($operation) {
            $this->run($operation)->willReturn(new \stdClass());

            throw new \RuntimeException('Operation failed');
        });

        $runner->run($operation)->shouldBeCalledTimes(1);
        $this->shouldThrow(\RuntimeException::class)->duringRun($operation);
    }
}
