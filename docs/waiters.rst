Waiters
=======

In any loop, you'll probably want to wait between calls somehow, to prevent DDoSing your other services
or 3rd party APIs.

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
