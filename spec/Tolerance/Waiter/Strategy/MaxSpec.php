<?php

namespace spec\Tolerance\Waiter\Strategy;

use Tolerance\Waiter\Strategy\MaxRetryException;
use Tolerance\Waiter\Strategy\WaitStrategy;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Waiter\WaiterException;

class MaxSpec extends ObjectBehavior
{
    function let(WaitStrategy $waitStrategy)
    {
        $this->beConstructedWith($waitStrategy, 1);
    }

    function it_is_a_wait_strategy()
    {
        $this->shouldHaveType(WaitStrategy::class);
    }

    function it_should_throw_directly_it_limit_is_zero(WaitStrategy $waitStrategy)
    {
        $this->beConstructedWith($waitStrategy, 0);
        $this->shouldThrow(WaiterException::class)->duringWait();
    }

    function it_should_throw_after_the_limit_is_reached(WaitStrategy $waitStrategy)
    {
        $this->beConstructedWith($waitStrategy, 2);

        $this->wait();
        $this->wait();
        $this->shouldThrow(MaxRetryException::class)->duringWait();
    }
}
