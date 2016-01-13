Request Identifier
==================

With many services talking to each other, it can quickly become a big pain to try to identify which original
call triggered calls to other services.

One solution to this problem is to use a common identifier across all the calls and requests,
so we could query all the logs related to a given identifier.

Tolerance comes with base classes in order to manage this request identifier object, as well as integrations with
Symfony, Guzzle and Monolog.

Generators
----------

These are the objects that generate the request identifiers. By default, Tolerance comes with :code:`UniqIdGenerator`
that generates the identifier using PHP's :code:`uniqid` function.

You can easily create your own generator by implementing the
`RequestIdentifierGenerator interface <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/RequestIdentifier/Generator/RequestIdentifierGenerator.php>`_.
You could implement a generator that creates UUID for instance.
All a generator needs to do is to return an object that implements the `RequestIdentifier interface <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/RequestIdentifier/RequestIdentifier.php>`_.
The easiest way is to directly use the `StringRequestIdentifier <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/RequestIdentifier/StringRequestIdentifier.php>`_.

.. code-block:: php

    use Tolerance\RequestIdentifier\StringRequestIdentifier;

    $identifier = StringRequestIdentifier::fromString('1234-[...]-0000');


Storage
-------

We need to store the request identifier somewhere. In-memory is the only implementation at the moment.
It simply stores the identifier in a property of an object.

.. code-block:: php

    use Tolerance\RequestIdentifier\InMemoryRequestIdentifierStorage;

    $storage = new InMemoryRequestIdentifierStorage();
    $storage->setRequestIdentifier($identifier);

    // ...
    $identifier = $storage->getRequestIdentifier();

Resolver
--------

This is probably the main object you'll work with as it's in charge of *resolving* which request identifier should be used at the moment.
Basically, the implementation that comes by default is the "stored or generated resolver". It gets the identifier from
the storage if it exists, otherwise it generates it and then stores it.


.. code-block:: php

    use Tolerance\RequestIdentifier\Resolver\StoredOrGeneratedResolver;

    $resolver = new StoredOrGeneratedResolver($storage, $generator);

    // ...
    $identifier = $resolver->get();

Symfony listener
----------------

The Symfony bridge comes with a request listener that listens to requests and gets the identifier from the header you
want. Next, it stores the identifier in the storage you've provided to it. You can have a look at the `code of the listener <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/Bridge/Symfony/RequestIdentifier/RequestHeadersListener.php>`_
or checkout the `Symfony Bridge documentation`_.

Monolog processor
-----------------

In the Monolog bridge, you'll find a processor that adds the request identifier in the context's tags of each log. The
request identifier come from the resolver that you needs in inject in it.

Checkout the `code of the processor <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/Bridge/Monolog/RequestIdentifier/RequestIdentifierProcessor.php>`_,
`Monolog's documentation <https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md#adding-extra-data-in-the-records>`_ or
the `Symfony Bridge documentation`_ that automatically registers the listener.

Guzzle middleware
-----------------

Guzzle 6, that follows PSR-7, allows to add a set of middleware to enhance the requests. Tolerance comes with a middleware
that adds the current request identifier in the headers of the sent requests.

Checkout the `code of the middleware factory <https://github.com/sroze/Tolerance/blob/master/src/Tolerance/Bridge/Guzzle/RequestIdentifier/MiddlewareFactory.php>`_,
`Guzzle's documentation <http://docs.guzzlephp.org/en/latest/handlers-and-middleware.html#middleware>`_ or
the `Symfony Bridge documentation`_ that automatically registers the middleware.

.. _Symfony Bridge documentation: bridges/symfony.html#request-identifier
