Operation Runners
=================

If you are using Tolerance's `operation runners <../operation-runner/>`_ you can decorate them with some additional
operation runner that will publish some metrics. It's an easy way to collect metrics from your application with almost
no effort.

Success/Failure
---------------

This operation runner will increment a ::code:`.failure` and a ::code:`.success` metric at every run. You can therefore
count the number of ran operation as well as their status.

.. code-block:: php

    use Tolerance\Operation\Runner\Metrics\SuccessFailurePublisherOperationRunner;

    $runner = new SuccessFailurePublisherOperationRunner(
        $decoratedRunner,
        $metricPublisher,
        'metric_namespace'
    );

.. note::

    You can also uses `Symfony's bridge <../bridges/symfony-bundle/metrics.html>`_ to create and use this runner without any PHP code.
