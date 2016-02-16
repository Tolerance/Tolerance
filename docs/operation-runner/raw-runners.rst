*Raw* runners
=============

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
