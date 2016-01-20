<?php

namespace spec\Tolerance\Throttling\RateMeasure;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Throttling\Rate\Rate;
use Tolerance\Throttling\RateMeasure\RateMeasure;

class ImmutableRateMeasureSpec extends ObjectBehavior
{
    function let(Rate $rate, \DateTime $dateTime)
    {
        $this->beConstructedWith($rate, $dateTime);
    }

    function it_is_a_rate_measure()
    {
        $this->shouldImplement(RateMeasure::class);
    }

    function it_exposes_the_rate(Rate $rate)
    {
        $this->getRate()->shouldReturn($rate);
    }

    function it_exposes_the_time(\DateTime $dateTime)
    {
        $this->getTime()->shouldReturn($dateTime);
    }
}
