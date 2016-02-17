Messages
========

This component is built to be able to track any kind of messages, by generating `MessageProfile <https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/MessageProfile/MessageProfile.php>`_
objects. At the moment, the library deals with the following messages:

- `HTTP`_ messages, both the PSR-7 and HttpFoundation implemantations
- `AMQP`_ messages

Obviously, you can create your ones by implementing `the interface <https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/MessageProfile/MessageProfile.php>`_.

HTTP
----

The HTTP messages are the most famous messages in the web industry. In order to be able to track them across services,
we are using an HTTP header. By default :code:`x-message-id`, it contains a unique identifier in the header of HTTP requests.

Two message profile factories are in Tolerance at the moment:

- `PSR-7 factory <https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/MessageProfile/HttpRequest/Psr7/SimplePsrProfileFactory.php>`_, the PHP-FIG request/response objects.
- `HttpFoundation factory <https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/MessageProfile/HttpRequest/HttpFoundation/Psr7BridgeProfileFactory.php>`_, Symfony's HTTP request/reponse component.

By integration with Guzzle and Symfony's HttpKernel component among others, we can ensure to add the :code:`x-message-id`
header each time we send a request, and then be able to know who sent and received it.

AMQP
----

In most of the large systems, asynchronous processes are powered by queuing mechanism based on AMQP messages. The tracking
mechanism is almost the same than for the HTTP messages as we are using AMQP's headers.

At the moment, a decorator for `RabbitMqBundle <https://github.com/videlalvaro/RabbitMqBundle>`_ consumers and producers
is available to get and set the request identifiers.

Timing
------

The message profiles comes with an optional MessageTiming. The interface only require to define the beginning and the
end of the message.

Peers
-----

A message is exchanged between two peers that needs to be identified.

