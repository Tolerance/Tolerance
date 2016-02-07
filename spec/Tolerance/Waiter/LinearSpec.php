<?php

namespace spec\Tolerance\Waiter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Waiter\Waiter;

class LinearSpec extends ObjectBehavior
{
    function let(Waiter $waiter)
    {
        $this->beConstructedWith($waiter, 3);
    }

    function it_is_a_waiter()
    {
        $this->shouldImplement(Waiter::class);
    }

    function it_always_wait_the_same_amount_of_time(Waiter $waiter)
    {
        $this->wait();
        $waiter->wait(3)->shouldHaveBeenCalled();

        $this->wait();
        $waiter->wait(3)->shouldHaveBeenCalled();
    }
}
