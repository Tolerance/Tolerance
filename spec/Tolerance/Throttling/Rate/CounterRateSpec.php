<?php

namespace spec\Tolerance\Throttling\Rate;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Throttling\Rate\Rate;

class CounterRateSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(12);
    }

    function it_is_a_rate()
    {
        $this->shouldImplement(Rate::class);
    }

    function it_exposes_the_ticks()
    {
        $this->getTicks()->shouldReturn(12);
    }
}
