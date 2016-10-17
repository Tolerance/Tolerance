Metrics
=======

The Symfony bundle comes with an integration for the `Metrics component <../../metrics/>`_ that allows you to easily
collect and publish metrics.

Collectors
----------

You can create metric collectors using YAML. The available types, at the moment, are the following:

- ::code:`rabbitmq`: will get some metrics about a RabbitMq queue, from the management API.

The following YAML is a reference of the possible configuration.

.. code-block:: yaml

    tolerance:
        metrics:
            collectors:
                my_queue:
                    type: rabbitmq
                    namespace: metric.prefix
                    options:
                        host: %rabbitmq_host%
                        port: %rabbitmq_management_port%
                        username: %rabbitmq_user%
                        password: %rabbitmq_password%
                        vhost: %rabbitmq_vhost%
                        queue: %rabbitmq_queue_name%


Publishers
----------

You can create publishers using YAML. The available types, at the moment, are the following:

- ::code:`hosted_graphite`: publish some metrics to the HostedGraphite service.
- ::code:`beberlei`: publish some metrics using a "collector" from `beberlei/metrics <https://github.com/beberlei/metrics>`_.

The following YAML is a reference of the possible configuration.

.. code-block:: yaml

    tolerance:
        metrics:
            publishers:
                hosted_graphite:
                    type: hosted_graphite
                    options:
                        server: %hosted_graphite_server%
                        port: %hosted_graphite_port%
                        api_key: %hosted_graphite_api_key%

                statsd:
                    type: beberlei
                    options:
                        service: beberlei_metrics.collector.statsd
                        auto_flush: true

Your own consumer and publishers
--------------------------------

If you want to register your own consumers and publishers to the default collection services, you have to tag your services
with the ::code:`tolerance.metrics.collector` and ::code:`tolerance.metrics.publisher` tags.

Command
-------

A command to collect and publish the metrics is built-in in the Bundle. As an example, you can run this command periodically
to be able to graph metrics from your application.

.. code-block:: shell

    app/console tolerance:metrics:collect-and-publish

If required, you can configure the collector and publisher used by this command:

.. code-block:: yaml

    tolerance:
        metrics:
            command:
                collector: tolerance.metrics.collector.collection
                publisher: tolerance.metrics.publisher.collection

Request
-------

If configured, the bundle will register listeners to send two metrics (a timing and an increment) at the end of each
request.

In other words, you just have to put this YAML configuration in order to publish metrics about the duration and the
number of requests to your Symfony application:

.. code-block:: yaml

    tolerance:
        metrics:
            request:
                namespace: my_api.http.request
                publisher: tolerance.metrics.publisher.statsd
