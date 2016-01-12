Waiters
=======

These are actual implementations of wait. The only for now is the `SleepWaiter` that calls `sleep` basically.

.. code-block:: php

    use Tolerance\Waiter\Waiter\SleepWaiter;

    $waiter = new SleepWaiter();

    // That will sleep for 500 milliseconds
    $waiter->sleep(0.5);
