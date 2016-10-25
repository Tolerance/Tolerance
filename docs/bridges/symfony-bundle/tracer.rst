Tracer
======

This integration is created to require you the less effort possible to use Tolerance's `Tracer component <../../tracer/>`_. Enable
it with the following configuration.

.. code-block:: yaml

    tolerance:
        tracer:
            service_name: MyApplicationService

            zipkin:
                http:
                    base_url: http://address.of.your.zipkin.example.com:9411


By default, it'll activate the following integrations:

- Request listener that reads the span informations from a request's header
- Monolog processor that adds the span information to the context of each log
- Registered Guzzle middleware that create a span when sending a request if you are using `CsaGuzzleBundle <https://github.com/csarrazi/CsaGuzzleBundle>`_
