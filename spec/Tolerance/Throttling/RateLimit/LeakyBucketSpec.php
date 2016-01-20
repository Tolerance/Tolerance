<?php

namespace spec\Tolerance\Throttling\RateLimit;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Throttling\Rate\Rate;
use Tolerance\Throttling\Rate\TimeRate;
use Tolerance\Throttling\RateLimit\RateLimit;
use Tolerance\Throttling\RateMeasure\RateMeasure;
use Tolerance\Throttling\RateMeasureStorage\RateMeasureStorage;

class LeakyBucketSpec extends ObjectBehavior
{
    function let(RateMeasureStorage $storage)
    {
        $this->beConstructedWith($storage, new TimeRate(10, TimeRate::PER_SECOND));
    }

    function it_is_a_rate_limit()
    {
        $this->shouldImplement(RateLimit::class);
    }

    function it_has_not_reached_the_limit_by_default(RateMeasureStorage $storage)
    {
        $storage->find('id')->willReturn(null);

        $this->hasReachedLimit('id')->shouldReturn(false);
    }

    function it_creates_and_saves_the_measure_after_a_tick_if_no_existing_measure(RateMeasureStorage $storage)
    {
        $storage->find('id')->willReturn(null);
        $storage->save('id', Argument::type(RateMeasure::class))->shouldBeCalled();

        $this->tick('id');
    }

    function it_increments_the_number_of_ticks(RateMeasureStorage $storage, RateMeasure $measure, Rate $rate)
    {
        $rate->getTicks()->willReturn(1);

        $measure->getTime()->willReturn(new \DateTime());
        $measure->getRate()->willReturn($rate);
        $storage->find('id')->willReturn($measure);
        $storage->save('id', Argument::type(RateMeasure::class))->shouldBeCalled();

        $this->tick('id');
    }
}
