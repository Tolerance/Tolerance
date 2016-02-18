*Raw* runners
=============

There's a set of *raw* operation runners that know how to run the default operations:

- The `callback runner`_ that is able to run callback operations.
- The `chain runner`_ that is able to chain operation runners that supports different operation types.

Callback runner
~~~~~~~~~~~~~~~

This is the runner that runs the Callback operations.

.. code-block:: php

    use Tolerance\Operation\Runner\CallbackOperationRunner;

    $runner = new CallbackOperationRunner();
    $result = $runner->run($operation);

Chain runner
~~~~~~~~~~~~

Constructed by other runners, usually the *raw* ones, it uses the first one that supports to run the operation.

.. code-block:: php

    use Tolerance\Operation\Runner\ChainOperationRunner;
    use Tolerance\Operation\Runner\CallbackOperationRunner;

    $runner = new ChainOperationRunner([
        new CallbackOperationRunner(),
    ]);

    $result = $runner->run($operation);

Also, the :code:`addOperationRunner` method allows you to add another runner on the fly.
