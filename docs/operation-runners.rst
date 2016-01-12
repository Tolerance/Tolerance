Operation runners
=================

Once you've created your `operation <operations.html>`_, you now have to run it using an operation runner.

First of all, you've the set of operation runners that knows how to run the default operation:

- `CallbackOperationRunner`_ that is able to run callback operations.
- `ChainOperationRunner`_ that is able to chain operation runners that supports different operation types.

In addition, there's few useful operation runners that decorates an existing one to add extra features:

- `RetryOperationRunner`_ will retry the operation until it is successful or considered as failing too much.
- `BufferedOperationRunner`_ will buffer operations and try to run them.

CallbackOperationRunner
-----------------------

This runner is the runner that supports to run the Callback operations.

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
    use Tolerance\Waiter\Strategy\WaitStrategy\Exponential;

    // Creates the strategy used to wait between failing calls
    $waitStrategy = new Max(
        new Exponential(
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

This runner will try to run the operations but if one fail, then it'll buffer it and then will try to
run it before the operation you'll add an other time.

.. code-block:: php

    use Tolerance\Operation\Buffer\InMemoryOperationBuffer;
    use Tolerance\Operation\Runner\BufferedOperationRunner;

    $buffer = new InMemoryOperationBuffer();
    $runner = new BufferedOperationRunner($runner, $buffer);

Then, you can try to run an operation:

.. code-block:: php

    // Let's say this operation will fail by throwing an exception
    $runner->run($operation);


If this operation fails (ie throws an exception) then the runner will keep it in the buffer. So when you'll try to run
another task, it'll **first** attempt to run the operation in the buffer.

.. code-block:: php

    $runner->run($secondOperation);

    // That will actually run the first one first,
    // and then the second one

Create your own
---------------

Despite the provided operation runners might be sufficient, you can easily create your own runner by implementing the
`OperationRunner interface <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/Operation/Runner/OperationRunner.php>`_.

All you need is to be able to run it and returns the operation.
