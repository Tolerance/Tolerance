<?php

namespace spec\Tolerance\Waiter;

use Tolerance\Waiter\Exception\CountLimitReached;
use Tolerance\Waiter\StatefulWaiter;
use Tolerance\Waiter\Waiter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Waiter\WaiterException;

class CountLimitedSpec extends ObjectBehavior
{
    function let(Waiter $waitStrategy)
    {
        $this->beConstructedWith($waitStrategy, 1);
    }

    function it_is_a_waiter()
    {
        $this->shouldHaveType(Waiter::class);
    }

    function it_is_a_stateful_waiter()
    {
        $this->shouldHaveType(StatefulWaiter::class);
    }

    function it_should_throw_directly_it_limit_is_zero(Waiter $waitStrategy)
    {
        $this->beConstructedWith($waitStrategy, 0);
        $this->shouldThrow(WaiterException::class)->duringWait();
    }

    function it_should_throw_after_the_limit_is_reached(Waiter $waitStrategy)
    {
        $this->beConstructedWith($waitStrategy, 2);

        $this->wait();
        $this->wait();
        $this->shouldThrow(CountLimitReached::class)->duringWait();
    }

    function it_propagates_the_state_reset(StatefulWaiter $waiter)
    {
        $this->beConstructedWith($waiter, 2);

        $waiter->resetState()->shouldBeCalled();
        $this->resetState();
    }
}
