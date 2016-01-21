<?php

namespace spec\Tolerance\Throttling\OperationRunner;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Throttling\OperationRunner\ThrottlingIdentifierStrategy;
use Tolerance\Throttling\RateLimit\RateLimit;
use Tolerance\Waiter\Waiter;

class RateLimitedOperationRunnerSpec extends ObjectBehavior
{
    function let(OperationRunner $runner, RateLimit $rateLimit, Waiter $waiter)
    {
        $this->beConstructedWith($runner, $rateLimit, $waiter);
    }

    function it_as_an_operation_runner()
    {
        $this->shouldImplement(OperationRunner::class);
    }

    function it_supports_the_operations_supported_if_its_decorated_runner_supports_it(OperationRunner $runner, Operation $operation)
    {
        $runner->supports($operation)->willReturn(true);
        $this->supports($operation)->shouldReturn(true);
    }

    function it_do_not_supports_the_operations_supported_if_its_decorated_runner_do_not_supports_it(OperationRunner $runner, Operation $operation)
    {
        $runner->supports($operation)->willReturn(false);
        $this->supports($operation)->shouldReturn(false);
    }

    function it_do_not_waits_if_the_rate_limit_is_not_reached(OperationRunner $runner, RateLimit $rateLimit, Waiter $waiter, Operation $operation)
    {
        $rateLimit->hasReachedLimit(Argument::any())->willReturn(false);
        $waiter->wait(Argument::any())->shouldNotBeCalled();
        $runner->run($operation)->shouldBeCalled();
        $rateLimit->tick(Argument::any())->shouldBeCalled();

        $this->run($operation);
    }

    function it_waits_the_number_of_required_tick_if_the_rate_limit_is_reached(OperationRunner $runner, RateLimit $rateLimit, Waiter $waiter, Operation $operation)
    {
        $rateLimit->hasReachedLimit(Argument::any())->willReturn(true);
        $rateLimit->getTicksBeforeUnderLimit(Argument::any())->willReturn(10);
        $waiter->wait(10)->shouldBeCalled();

        $runner->run($operation)->shouldBeCalled();
        $rateLimit->tick(Argument::any())->shouldBeCalled();

        $this->run($operation);
    }

    function its_identifier_strategy_can_be_override(OperationRunner $runner, RateLimit $rateLimit, Waiter $waiter, Operation $operation, ThrottlingIdentifierStrategy $identifierStrategy)
    {
        $this->beConstructedWith($runner, $rateLimit, $waiter, $identifierStrategy);

        $identifierStrategy->getOperationIdentifier($operation)->willReturn('my-id');

        $rateLimit->hasReachedLimit('my-id')->willReturn(true);
        $rateLimit->getTicksBeforeUnderLimit('my-id')->willReturn(10);
        $waiter->wait(10)->shouldBeCalled();

        $runner->run($operation)->shouldBeCalled();
        $rateLimit->tick('my-id')->shouldBeCalled();

        $this->run($operation);
    }
}
