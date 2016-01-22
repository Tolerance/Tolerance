Throttling
==========

The principle of throttling a set of operation is to restrict the maximum number of these operations to run
in a given time frame.

For instance, we want to be able to run a maximum of 10 requests per seconds per client. That means that the operations
tagged as "coming from the client X" have to be throttled with a rate of 10 requests per seconds. It is important
to note that the time frame can also be unknown and you can use your own *ticks* to achieve a rate limitation for
concurrent processes for instance.

This document contains the following sections:

- `Rate`_ explains the :code:`Rate` interface and its implementations
- `Rate measure & Storage`_ explains the rate measures and the storage strategies for it
- `Implementations`_ explains the different throttling strategies embedded
- `Waiter`_ presents a waiter that waits the required amount to match the rate limit
- `Operation Runner`_ presents an operation runner that runs the operations at the given rate limit

Rate
----

Even if you may not need to extend these main objects of the Throttling, here are the description of the :code:`Rate`
and :code:`RateMeasure` objects that are used by the rate limit implementations.

The :code:`Rate` interface simply defines a :code:`getTicks()` method that should returns a number. The first implementation is the
:code:`TimeRate` that defines a number of operation in a given time range.

.. code-block:: php

    use Tolerance\Throttling\Rate\TimeRate;

    $rate = new TimeRate(60, TimeRate::PER_SECOND)
    $rate = new TimeRate(1, TimeRate::PER_MINUTE)

The second implementation is the :code:`CounterRate` that simply defines a counter. This is mainly used to store a
counter such as in the internals of the `Leaky Bucket`_ implementation or when you'll want to setup a rate limitation
for parallel running processes for instance.

Rate measure & Storage
----------------------

The :code:`RateMeasure` is mainly used in the internals to store a given :code:`Rate` at a given time. The only
implementation at the moment is the :code:`ImmutableRateMeasure`.

What you have to care about is the storage of these rate measures because they need to be stored in order to ensure
the coherency or this rate limits, especially when running with concurrent requests.

In memory storage
~~~~~~~~~~~~~~~~~

The easiest way to start is to store the rate measures in memory. The major drawback is that in order to ensure your
rate limitation you need to have your application running in a single long-running script.

.. code-block:: php

    use Tolerance\Throttling\RateMeasureStorage\InMemoryStorage;

    $storage = new InMemoryStorage();

Implementations
---------------

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
~~~~~~~~~~~~

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

You can have a look to the `LeakyBucket unit tests <https://github.com/sroze/Tolerance/blob/master/tests/Tolerance/Throttling/RateLimit/LeakyBucketTest.php>`_
to have a better idea of how you can use it directly.

Integrations
------------

Once you've chosen your rate limit strategy you can either use it directly or integrates it with some of the
existing components of Tolerance.

- `Waiter`_ is a :code:`Waiter` that waits the required amount to match the rate limit.
- `Operation Runner`_ is an :code:`Operation Runner` that will run your operations based on the rate limit.

Waiter
~~~~~~

Using the Rate Limit Waiter, you will just have to call the :code:`wait()` method of the waiter at the end of all your
iterations in a loop for instance, to ensure that each the iteration rate will match the rate limit you've defined.

.. code-block:: php

    use Tolerance\Throttling\Rate\TimeRate;
    use Tolerance\Throttling\RateLimit\LeakyBucket;
    use Tolerance\Throttling\RateMeasureStorage\InMemoryStorage;
    use Tolerance\Throttling\Waiter\RateLimitWaiter;
    use Tolerance\Waiter\SleepWaiter;

    $rate = new TimeRate(10, TimeRate::PER_SECOND);
    $rateLimit = new LeakyBucket(new InMemoryStorage(), $rate);
    $waiter = new RateLimitWaiter($rateLimit, new SleepWaiter());

    for ($i = 0; $i < 100; $i++) {
        echo microtime(true)."\n";

        $waiter->wait('id');
    }

The *optional* argument of the :code:`wait` method is the identifier of the operation you want to isolate. That means
that you can use the same waiter/rate limit for different type of operations if you want.

Operation Runner
~~~~~~~~~~~~~~~~

The Rate Limited Operation Runner is the integration of rate limiting with operation runners. That way you can ensure
that all the operations you want to run will actually run at the given time rate.

.. code-block:: php

    $rateLimit = /* The implementation you wants */;

    $operationRunner = new RateLimitedOperationRunner(
        new SimpleOperationRunner(),
        $rateLimit,
        new SleepWaiter()
    );

    $operationRunner->run($operation);

By default, the identifier given to the rate limit is an empty string. The *optional* fourth parameter is an
object implementing the :code:`ThrottlingIdentifierStrategy` interface that will returns the identifier of the operation.

.. code-block:: php

    class ThrottlingIdentifierStrategy implements ThrottlingIdentifierStrategy
    {
        /**
         * {@inheritdoc}
         */
        public function getOperationIdentifier(Operation $operation)
        {
            if ($operation instanceof MyClientOperation) {
                return sprintf(
                    'client-%s',
                    $operation->getClient()->getIdentifier()
                );
            }

            return 'unknown-client';
        }
    }
