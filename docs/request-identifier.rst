Request Identifier
==================

If you've many services talking each other, it can quickly become a big pain to try to identify which original
call created which ones on other services.

One thing that can be done is to uses a common identifier across all the calls and the requests in order to be able
to query all the logs related to this identifier.

Tolerance comes with base classes in order to manage this request identifier object, as well as integrations with
Symfony, Guzzle and Monolog.

Generators
----------

These are the objects that will generate the request identifiers. By default, Tolerance come from the :code:`UniqIdGenerator`
that generates the identifier using PHP's :code:`uniqid` function.

You can easily create your own if you want to use an UUID for instance by implementing the
`RequestIdentifierGenerator interface <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/RequestIdentifier/Generator/RequestIdentifierGenerator.php>`_.
All you need is them returning an object that implements the `RequestIdentifier interface <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/RequestIdentifier/RequestIdentifier.php>`_
or directly using the `StringRequestIdentifier <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/RequestIdentifier/StringRequestIdentifier.php>`_.

.. code-block:: php

    use Tolerance\RequestIdentifier\StringRequestIdentifier;

    $identifier = StringRequestIdentifier::fromString('1234-[...]-0000');


Storage
-------

We need to store the request identifier somewhere. The only implementation is at the moment an in-memory implementation
that simply stores the identifier in a property of the object.

.. code-block:: php

    use Tolerance\RequestIdentifier\InMemoryRequestIdentifierStorage;

    $storage = new InMemoryRequestIdentifierStorage();
    $storage->setRequestIdentifier($identifier);

    // ...
    $identifier = $storage->getRequestIdentifier();

Resolver
--------

This is probably the main object you'll use as it is in charge of *resolving* what is the request identifier to use at the moment.
Basically, the implementation that comes by default is the "stored or generated resolver" that get the identifier from
the storage is it exists, else generate it and store it.


.. code-block:: php

    use Tolerance\RequestIdentifier\Resolver\StoredOrGeneratedResolver;

    $resolver = new StoredOrGeneratedResolver($storage, $generator);

    // ...
    $identifier = $resolver->get();

Symfony listener
----------------

The Symfony bridge contains a request listeners that listeners to requests and gets the identifier from the header you
want, then store it in the storage you've provided to it. You can have a look to the `code if the listener <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/Bridge/Symfony/RequestIdentifier/RequestHeadersListener.php>`_
or checkout the `Symfony Bridge documentation <bridges/symfony.html>`_.
