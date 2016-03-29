<?php

namespace spec\Tolerance\Throttling\Rate;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Throttling\Rate\Rate;
use Tolerance\Throttling\Rate\TimeRate;

class TimeRateSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(1, TimeRate::PER_SECOND);
    }

    function it_is_a_rate()
    {
        $this->shouldImplement(Rate::class);
    }

    function it_exposes_the_ticks_as_seconds()
    {
        $this->getTicks()->shouldReturn(1);
    }

    function it_can_be_constructed_with_minutes()
    {
        $this->beConstructedWith(1, TimeRate::PER_MINUTE);
        $this->getTicks()->shouldReturn(1/60);
    }
}
