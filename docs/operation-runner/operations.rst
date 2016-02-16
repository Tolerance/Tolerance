Operations
==========

An operation is an atomic piece of processing. This is for instance an API call to an third-party service, or a process
that requires to talk to the database. We can use them for any process that is dependent on a non-trusted resource,
starting with the network connection.

From a callback
---------------

The first kind of operation is an operation defined by a PHP callable.
This operation can be created with the :code:`Callback` class, like this:

.. code-block:: php

    use Tolerance\Operation\Callback;

    $operation = new Callback(function() use ($client) {
        return $client->get('/foo');
    });

This class accepts any supported `PHP callable <http://php.net/manual/en/language.types.callable.php>`_, so you can also
use object methods. For instance:

.. code-block:: php

    use Tolerance\Operation\Callback;

    $operation = new Callback([$this, 'run']);

