<?php

namespace spec\Tolerance\Operation\Runner;

use Tolerance\Operation\ExceptionCatcher\ExceptionCatcherVoter;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Operation\Runner\RetryOperationRunner;
use Tolerance\Waiter\StatefulWaiter;
use Tolerance\Waiter\WaiterException;
use Tolerance\Waiter\Waiter;
use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\ErrorException;
use Prophecy\Argument;

class RetryOperationRunnerSpec extends ObjectBehavior
{
    function let(OperationRunner $runner, Waiter $waiter)
    {
        $this->beConstructedWith($runner, $waiter);
    }

    function it_should_be_an_operation_runner()
    {
        $this->shouldHaveType(OperationRunner::class);
    }

    function it_should_trigger_deprecation_error_on_exception_voter(OperationRunner $runner, Waiter $waitStrategy, ExceptionCatcherVoter $voter)
    {
        $message = sprintf('%s is deprecated, you should implement %s instead', ExceptionCatcherVoter::class, ThrowableCatcherVoter::class);

        $this->beConstructedWith($runner, $waitStrategy, $voter);
        $this->shouldThrow(ErrorException::class)->duringInstantiation();
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

    function it_should_throw_the_original_exception_if_the_wait_fails(OperationRunner $runner, Waiter $waiter, Operation $operation)
    {
        $runner->run($operation)->will(function() use ($operation) {
            throw new \RuntimeException('Operation failed');
        });

        $waiter->wait()->willThrow(new WaiterException('Retried to many times'));
        $this->shouldThrow(\RuntimeException::class)->duringRun($operation);
    }

    function it_should_not_retry_if_the_optional_catcher_voter_says_no(OperationRunner $runner, Operation $operation, Waiter $waiter, ThrowableCatcherVoter $throwableCatcherVoter)
    {
        $this->beConstructedWith($runner, $waiter, $throwableCatcherVoter);

        $throwableCatcherVoter->shouldCatchThrowable(Argument::any())->willReturn(false)->shouldBeCalled();
        $runner->run($operation)->will(function() use ($operation) {
            $this->run($operation)->willReturn(new \stdClass());

            throw new \RuntimeException('Operation failed');
        });

        $runner->run($operation)->shouldBeCalledTimes(1);
        $this->shouldThrow(\RuntimeException::class)->duringRun($operation);
    }

    function it_should_reset_the_state_of_any_stateful_waiter(StatefulWaiter $statefulWaiter, OperationRunner $runner, Operation $operation)
    {
        $this->beConstructedWith($runner, $statefulWaiter);

        $statefulWaiter->resetState()->shouldBeCalled();

        $runner->run($operation)->willReturn(true);
        $this->run($operation)->shouldReturn(true);
    }

    function it_should_reset_the_state_only_once(StatefulWaiter $statefulWaiter, OperationRunner $runner, Operation $operation)
    {
        $this->beConstructedWith($runner, $statefulWaiter);

        $runner->run($operation)->will(function() use ($operation) {
            $this->run($operation)->willReturn(new \stdClass());

            throw new \RuntimeException('Operation failed');
        });

        $runner->run($operation)->shouldBeCalledTimes(2);
        $statefulWaiter->wait()->willReturn(null);
        $statefulWaiter->resetState()->shouldBeCalledTimes(1);

        $this->run($operation);
    }
}
