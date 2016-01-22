<?php

namespace spec\Tolerance\Throttling\Waiter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Throttling\RateLimit\RateLimit;
use Tolerance\Waiter\Waiter;

class RateLimitWaiterSpec extends ObjectBehavior
{
    function let(RateLimit $rateLimit, Waiter $waiter)
    {
        $this->beConstructedWith($rateLimit, $waiter);
    }

    function it_is_a_waiter()
    {
        $this->shouldImplement(Waiter::class);
    }

    function it_simply_ticks_the_rate_limit_if_the_limit_was_not_reached(RateLimit $rateLimit)
    {
        $rateLimit->hasReachedLimit('id')->willReturn(false);
        $rateLimit->tick('id')->shouldBeCalled();

        $this->wait('id');
    }

    function it_waits_if_the_limit_is_reached(RateLimit $rateLimit, Waiter $waiter)
    {
        $rateLimit->hasReachedLimit('id')->willReturn(true);
        $rateLimit->getTicksBeforeUnderLimit('id')->willReturn(1.234);
        $waiter->wait(1.234)->shouldBeCalled();
        $rateLimit->tick('id')->shouldBeCalled();

        $this->wait('id');
    }
}
