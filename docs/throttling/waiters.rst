Waiters
=======

In any loop, you'll probably want to wait between calls somehow, to prevent DDoSing your other services
or 3rd party APIs. Tolerance come with 2 default *raw* waiters:

- `SleepWaiter`_ that simply wait using PHP's `usleep` function
- `NullWaiter`_ that do not wait and it mainly used for tests

Once you are able to wait an amount of time, you may want to surcharge the waiters to apply different wait strategies
such as an exponential back-off.

- The `linear` waiter simply waits a predefined amount of time.
- The `exponential back-off`_ waiter uses the well-known `Exponential backoff algorithm <https://en.wikipedia.org/wiki/Exponential_backoff>`_
  to multiplicatively increase the amount of time of wait time.
- The `count limited`_ waiter simply adds a limit in the number of times it can be called.
- The `rate limit`_ waiter will wait the required amount of time to satisfy a rate limit.

.. note::

    The Throttling component also come with a `Rate Limited Operation Runner <integrations.html#operation-runner>`_

SleepWaiter
-----------

This implementation will use PHP's :code:`sleep` function to actually pause your process for a given amount of time.

.. code-block:: php

    use Tolerance\Waiter\Waiter\SleepWaiter;

    $waiter = new SleepWaiter();

    // That will sleep for 500 milliseconds
    $waiter->wait(0.5);

NullWaiter
----------

The :code:`NullWaiter` won't actually wait anything. This is usually used for the testing, you should be careful
using it in production.


.. code-block:: php

    use Tolerance\Waiter\Waiter\NullWaiter;

    $waiter = new NullWaiter();

Linear
------

How to simply always wait a predefined amount of time? There's the linear waiter. The following example show how
it can be used to have a waiter that will always wait 0.1 seconds.

.. code-block:: php

    use Tolerance\Waiter\Waiter\SleepWaiter;
    use Tolerance\Waiter\Waiter\Linear;

    $waiter = new Linear(new SleepWaiter(), 0.1);


Exponential back-off
--------------------

.. pull-quote::

    In a variety of computer networks, binary exponential backoff or truncated binary exponential backoff refers to an
    algorithm used to space out repeated retransmissions of the same block of data, often as part of network congestion
    avoidance.

    -- `Wikipedia <https://en.wikipedia.org/wiki/Exponential_backoff>`_

The :code:`ExponentialBackOff` waiter decorates one of the raw waiters to add this additional exponential
wait time.

.. code-block:: php

    use Tolerance\Waiter\Waiter\ExponentialBackOff;
    use Tolerance\Waiter\Waiter\SleepWaiter;

    // We use an initial collision number of 0
    $waiter = new SleepWaiter();
    $waitStrategy = new ExponentialBackOff($waiter, 0);

    $waitStrategy->wait(); // 0.5s
    $waitStrategy->wait(); // 1.5s
    $waitStrategy->wait(); // 3.5s

    // ...

Count limited
-------------

This decoration strategy defines a maximum amount of waits. Once this limit is reached, it will
throw the :code:`CountLimitReached` exception.

.. code-block:: php

    // Wait for a maximum amount of 10 times
    $waitingStrategy = new CountLimited($waitingStrategy, 10);

Rate Limit
----------

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

Time Out
--------

This decoration strategy defines a time out to your operation execution. Once this time out is exceeded, it will
throw the :code:`TimedOutExceeded` exception.

.. code-block:: php

    // Time out in 20 seconds
    $waitingStrategy = new TimeOut($waitingStrategy, 20);
