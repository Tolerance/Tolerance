Operation runners
=================

In order to run the different applications, you can use and combine different operation runners. The list bellow
describes the different operation runners available:

- `SimpleOperationRunner`_ that simply calls the operation
- `BufferedOperationRunner`_ will buffer operations and try to run them.
- `RetryOperationRunner`_ will retry the operation until it is successful.

SimpleOperationRunner
---------------------

That's the simplest operation runner ever. It calls :code:`run()` on the operation.

.. code-block:: php

    use Tolerance\Operation\Runner\SimpleOperationRunner;

    $runner = new SimpleOperationRunner();
    $runner->run($operation);

BufferedOperationRunner
-----------------------

The idea of this runner is to try running the operations but if not possible, then it'll buffer it and then will try to
run it before the operation you'll add an other time.

.. code-block:: php

    use Tolerance\Operation\Buffer\InMemoryOperationBuffer;
    use Tolerance\Operation\Runner\SimpleOperationRunner;
    use Tolerance\Operation\Runner\BufferedOperationRunner;

    $buffer = new InMemoryOperationBuffer();
    $runner = new BufferedOperationRunner(new SimpleOperationRunner(), $buffer);

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

RetryOperationRunner
--------------------

This runner will retry to run the operation until it is successful or the wait strategy decide to fail. Again, this
should be used as decorator as an existing operation runner.

.. code-block:: php

    use Tolerance\Operation\Runner\SimpleOperationRunner;
    use Tolerance\Operation\Runner\RetryOperationRunner;
    use Tolerance\Waiter\Waiter\SleepWaiter;
    use Tolerance\Waiter\Strategy\WaitStrategy\Exponential;

    // This example will run the operation until it is successful
    // and will wait an exponential amount of time between the calls.

    $runner = new SimpleOperationRunner();
    $waitStrategy = new Exponential(new SleepWaiter(), 1);
    $runner = new RetryOperationRunner($runner, $waitStrategy);

    $runner->run($operation);

**Note:** you should decorate your waiting strategy by the `Max strategy <wait-strategies.html#max-strategy>`_ in order to prevent infinite or extremely long loops.
