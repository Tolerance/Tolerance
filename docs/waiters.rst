Waiters
=======

In any loop, you'll probably want to wait between calls somehow, to prevent DDoSing your other services
or 3rd party APIs. Tolerance come with 2 default _raw_ waiters:

- `SleepWaiter`_ that simply wait using PHP's `usleep` function
- `NullWaiter`_ that do not wait and it mainly used for tests

Once you are able to wait an amount of time, you may want to surcharge the waiters to apply different wait strategies
such as an exponential back-off.

- The `exponential back-off`_ waiter uses the well-known `Exponential backoff algorithm <https://en.wikipedia.org/wiki/Exponential_backoff>`_
  to multiplicatively increase the amount of time of wait time.
- The `count limited`_ waiter simply adds a limit in the number of times it can be called.

SleepWaiter
-----------

This implementation will use PHP's :code:`sleep` function to actually pause your process for a given amount of time.

.. code-block:: php

    use Tolerance\Waiter\Waiter\SleepWaiter;

    $waiter = new SleepWaiter();

    // That will sleep for 500 milliseconds
    $waiter->sleep(0.5);

NullWaiter
----------

The :code:`NullWaiter` won't actually wait anything. This is usually used for the testing, you should be careful
using it in production.


.. code-block:: php

    use Tolerance\Waiter\Waiter\NullWaiter;

    $waiter = new NullWaiter();


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
