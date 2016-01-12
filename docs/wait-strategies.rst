Wait strategies
===============

Many different wait strategies can be used in order to retry things or simply wait for a circuit to be closed, etc...

Exponential
-----------

Each time you will call `wait` on the object, it'll wait an exponential number of seconds, based on your exponent.

.. code-block:: php

    use Tolerance\Waiter\Strategy\WaitStrategy\Exponential;
    use Tolerance\Waiter\Waiter\SleepWaiter;

    // We use an initial exponent of 1
    $waiter = new SleepWaiter();
    $waitStrategy = new Exponential($waiter, 1);

    // Waits exp(1)
    $waitStrategy->wait();

    // Waits exp(2)
    $waitStrategy->wait();

    // Waits exp(3)
    $waitStrategy->wait();

    // ...

Max strategy
------------

This decoration strategy defines a maximum amount of waits.

.. code-block:: php

    // Wait for a maximum amount of 10 times
    $waitingStrategy = new Max($waitingStrategy, 10);
