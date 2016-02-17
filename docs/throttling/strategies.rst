Strategies
==========

There are many existing algorithms for throttling, you need to choose the one that fits the best your needs.
At the moment, only the following algorithm can be found in Tolerance:

- `Leaky bucket`_, a *rolling time frame* rate limit

Each implementation implements the :code:`RateLimit` interface that contains the following methods:

- :code:`hasReachedLimit(string $identifier)`: :code:`bool`
  Returns true if the given identifier reached the limit

- :code:`getTicksBeforeUnderLimit(string $identifier)`: :code:`float`
  Returns the number of ticks that represents the moment when the rate will be under the limit.

- :code:`tick(string $identifier)`
  Register a tick on the bucket, meaning that an operation was executed


Leaky bucket
------------

The `leaky bucket algorithm <https://en.wikipedia.org/wiki/Leaky_bucket>`_ ensure that the number of operations won't
exceed a rate on a given **rolling time frame**.

.. code-block:: php

    use Tolerance\Throttling\Rate\TimeRate;
    use Tolerance\Throttling\RateLimit\LeakyBucket;
    use Tolerance\Throttling\RateMeasureStorage\InMemoryStorage;

    $rateLimit = new LeakyBucket(
        new InMemoryStorage(),
        new TimeRate(10, TimeRate::PER_SECOND)
    );

You can have a look to the `LeakyBucket unit tests <https://github.com/Tolerance/Tolerance/blob/master/tests/Tolerance/Throttling/RateLimit/LeakyBucketTest.php>`_
to have a better idea of how you can use it directly.
