<?php

namespace Tolerance\Throttling\RateLimit;

use Tolerance\Throttling\Rate\TimeRate;
use Tolerance\Throttling\RateMeasureStorage\InMemoryStorage;

class LeakyBucketTest extends \PHPUnit_Framework_TestCase
{
    public function test_that_it_do_not_exceed_the_number_of_operations()
    {
        $bucket = new LeakyBucket(
            new InMemoryStorage(),
            new TimeRate(10, TimeRate::PER_SECOND)
        );

        $this->assertFalse($bucket->hasReachedLimit('id'));

        for ($i = 0; $i < 10; $i++) {
            $this->assertFalse($bucket->hasReachedLimit('id'), 'Reached limit at tick #'.$i);
            $bucket->tick('id');
        }

        $this->assertTrue($bucket->hasReachedLimit('id'));
    }

    /**
     * @large
     */
    public function test_it_returns_the_number_of_seconds_before_being_under_limit()
    {
        $expectedCalculatedRateInSeconds = 10;
        $numberOfTicks = 100;

        $expectedTimeInSeconds = $numberOfTicks / $expectedCalculatedRateInSeconds;
        $rate = new TimeRate($expectedCalculatedRateInSeconds, TimeRate::PER_SECOND);

        $bucket = new LeakyBucket(
            new InMemoryStorage(),
            $rate
        );

        $startTime = microtime(true);

        for ($j = 0; $j < $numberOfTicks; $j++) {
            $this->assertFalse($bucket->hasReachedLimit('id'));
            $bucket->tick('id');

            if ($bucket->hasReachedLimit('id')) {
                usleep(1000 * 1000 * $bucket->getTicksBeforeUnderLimit('id'));
            }
        }

        $elapsedTime = microtime(true) - $startTime;
        $calculatedRate = $numberOfTicks / $elapsedTime;

        // Time can only be more
        $this->assertGreaterThan($expectedTimeInSeconds, $elapsedTime);

        // Rate rate only be less
        $this->assertLessThan($expectedCalculatedRateInSeconds, $calculatedRate);
    }
}
