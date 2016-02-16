*Behavioural* runners
=====================

Retry runner
------------

This runner will retry to run the operation until it is successful or the wait strategy decide to fail.

.. code-block:: php

    use Tolerance\Operation\Runner\CallbackOperationRunner;
    use Tolerance\Operation\Runner\RetryOperationRunner;
    use Tolerance\Waiter\Waiter\SleepWaiter;
    use Tolerance\Waiter\Waiter\ExponentialBackOff;

    // Creates the strategy used to wait between failing calls
    $waitStrategy = new CountLimited(
        new ExponentialBackOff(
            new SleepWaiter(),
            1
        ),
        10
    );

    // Creates the runner
    $runner = new RetryOperationRunner(
        new CallbackOperationRunner(),
        $waitStrategy
    );

    $runner->run($operation);


Buffered runner
---------------

This runner will buffer all the operations to post-pone their execution.

.. code-block:: php

    use Tolerance\Operation\Buffer\InMemoryOperationBuffer;
    use Tolerance\Operation\Runner\BufferedOperationRunner;

    $buffer = new InMemoryOperationBuffer();
    $bufferedRunner = new BufferedOperationRunner($runner, $buffer);

    // These 2 operations will be buffered
    $bufferedRunner->run($firstOperation);
    $bufferedRunner->run($secondOperation);

Once you've decided that you want to run all the operations, you need to call the :code:`runBufferedOperations` method.

.. code-block:: php

    $bufferedRunner->runBufferedOperations();

.. tip::

    The Symfony Bridge automatically run all the buffered operations when the kernel terminates. Checkout the
    `Symfony Bridge documentation <../bridges/symfony/intro.html>`_
