<?php

namespace spec\Tolerance\Waiter;

use Tolerance\Waiter\StatefulWaiter;
use Tolerance\Waiter\Waiter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExponentialBackOffSpec extends ObjectBehavior
{
    function let(Waiter $waiter)
    {
        $this->beConstructedWith($waiter, 1);
    }

    function it_is_a_stateful_waiter()
    {
        $this->shouldHaveType(StatefulWaiter::class);
    }

    function it_waits_an_exponential_amount_of_time_each_time_its_called(Waiter $waiter)
    {
        $waiter->wait(exp(1))->shouldBeCalled();
        $this->wait();

        $waiter->wait(exp(2))->shouldBeCalled();
        $this->wait();

        $waiter->wait(exp(3))->shouldBeCalled();
        $this->wait();
    }

    function it_waits_a_fraction_of_a_second(Waiter $waiter)
    {
        $this->beConstructedWith($waiter, 0, 0.5);

        $waiter->wait(exp(0))->shouldBeCalled();
        $this->wait();

        $waiter->wait(exp(0.5))->shouldBeCalled();
        $this->wait();

        $waiter->wait(exp(1))->shouldBeCalled();
        $this->wait();
    }
}
