Operations
==========

An operation is an atomic piece of processing. This is for instance an API call to an third-party service.
You can defines an operation by using the callback method, like this:

.. code-block:: php

    use Tolerance\Operation\Callback;

    $operation = new Callback(function() use ($client) {
        return $client->get('/foo');
    });
