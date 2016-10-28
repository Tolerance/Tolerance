<?php

namespace spec\Tolerance\Waiter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Waiter\Waiter;
use Tolerance\Waiter\StatefulWaiter;
use Tolerance\Waiter\Exception\TimedOutExceeded;

class TimeOutSpec extends ObjectBehavior
{
    function let(Waiter $waitStrategy)
    {
        $this->beConstructedWith($waitStrategy, 10);
    }

    function it_is_a_waiter()
    {
        $this->shouldHaveType(Waiter::class);
    }

    function it_is_a_stateful_waiter()
    {
        $this->shouldHaveType(StatefulWaiter::class);
    }

    function it_delegates_to_other_waiter_when_waiting(Waiter $waitStrategy)
    {
        $this->wait(1);
        $waitStrategy->wait(1)->shouldHaveBeenCalled();
    }

    function it_stops_execution_if_time_out_value_is_exceeded(Waiter $waitStrategy)
    {
        $this->wait(5);
        $this->shouldThrow(TimedOutExceeded::class)->duringWait(5);
        $waitStrategy->wait(5)->shouldHaveBeenCalled();
    }

    function it_resets_its_state()
    {
        $this->wait(5);
        $this->resetState();
        $this->wait(5);
    }
}
