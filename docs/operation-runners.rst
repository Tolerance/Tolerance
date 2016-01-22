Operation runners
=================

Once you've created an `operation <operations.html>`_, you now have to run it using an operation runner. First of all,
there's a set of *raw* operation runners that know how to run the default operations:

- The `callback runner`_ that is able to run callback operations.
- The `chain runner`_ that is able to chain operation runners that supports different operation types.

In addition, there's a few useful operation runners that decorate an existing one to add extra *behaviour*:

- The `retry runner`_ will retry the operation until it is successful or considered as failing too much.
- The `buffered runner`_ will buffer operations until you decide the run them.

.. note::

    The `Throttling component <throttling.html>`_ also come with a `Rate Limited Operation Runner <throttling.html#operation-runner>`_


*Raw* runners
-------------

Callback runner
~~~~~~~~~~~~~~~

This is the runner that runs the Callback operations.

.. code-block:: php

    use Tolerance\Operation\Runner\CallbackOperationRunner;

    $runner = new CallbackOperationRunner();
    $runner->run($operation);

Chain runner
~~~~~~~~~~~~

Constructed by other runners, usually the *raw* ones, it uses the first one that supports to run the operation.

.. code-block:: php

    use Tolerance\Operation\Runner\ChainOperationRunner;
    use Tolerance\Operation\Runner\CallbackOperationRunner;

    $runner = new ChainOperationRunner([
        new CallbackOperationRunner(),
    ]);

    $runner->run($operation);

Also, the :code:`addOperationRunner` method allows you to add another runner on the fly.

*Behavioural* runners
---------------------

Retry runner
~~~~~~~~~~~~

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
~~~~~~~~~~~~~~~

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
    `Symfony Bridge documentation <bridges/symfony.html>`_

Create your own
---------------

Provided operation runners might be sufficient in many cases, but you can easily create your own runners by implementing the
`OperationRunner interface <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/Operation/Runner/OperationRunner.php>`_.

