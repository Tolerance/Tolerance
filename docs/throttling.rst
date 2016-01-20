Throttling
==========

.. code-block:: php

    use Tolerance\Throttling\Rate\TimeRate;
    use Tolerance\Throttling\RateLimit\LeakyBucket;
    use Tolerance\Throttling\RateMeasureStorage\InMemoryStorage;
    use Tolerance\Throttling\Waiter\RateLimitWaiter;
    use Tolerance\Waiter\SleepWaiter;

    return new RateLimitWaiter(
        new LeakyBucket(
            new InMemoryStorage(),
            new TimeRate(10, TimeRate::PER_SECOND)
        ),
        new SleepWaiter()
    );
