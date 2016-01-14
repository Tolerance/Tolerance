Operation runners
=================

Once you've created your `operation <operations.html>`_, you now have to run it using an operation runner.

First of all, there's a set of operation runners that know how to run the default operation:

- `CallbackOperationRunner`_ that is able to run callback operations.
- `ChainOperationRunner`_ that is able to chain operation runners that supports different operation types.

In addition, there's a few useful operation runners that decorate an existing one to add extra features:

- `RetryOperationRunner`_ will retry the operation until it is successful or considered as failing too much.
- `BufferedOperationRunner`_ will buffer operations and try to run them.

CallbackOperationRunner
-----------------------

This is the runner that runs the Callback operations.

.. code-block:: php

    use Tolerance\Operation\Runner\CallbackOperationRunner;

    $runner = new CallbackOperationRunner();
    $runner->run($operation);

ChainOperationRunner
--------------------

Construct the runner with a bunch of runners that knows how to run the different type of operations you want to run
and it'll ask the good one.

.. code-block:: php

    use Tolerance\Operation\Runner\ChainOperationRunner;
    use Tolerance\Operation\Runner\CallbackOperationRunner;

    $runner = new ChainOperationRunner([
        new CallbackOperationRunner(),
    ]);

    $runner->run($operation);

Also, the :code:`addOperationRunner` method allows you to add another runner on the fly.

RetryOperationRunner
--------------------

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


BufferedOperationRunner
-----------------------

This runner will buffer failed operations and try to re-run them before any newly scheduled operations.

.. code-block:: php

    use Tolerance\Operation\Buffer\InMemoryOperationBuffer;
    use Tolerance\Operation\Runner\BufferedOperationRunner;

    $buffer = new InMemoryOperationBuffer();
    $runner = new BufferedOperationRunner($runner, $buffer);

Then, you can try to run an operation:

.. code-block:: php

    // Let's say this operation fails by throwing an exception
    $runner->run($operation);


If this operation fails (ie throws an exception) then the runner will keep it in the buffer. When you try to run
another task, it'll **first** attempt to run the operation in the buffer.

.. code-block:: php

    $runner->run($secondOperation);

    // That will actually run the first one first,
    // and then the second one

Create your own
---------------

Provided operation runners might be sufficient in many cases, but you can easily create your own runners by implementing the
`OperationRunner interface <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/Operation/Runner/OperationRunner.php>`_.

