Publishers
==========

Tolerance have built-in publishers that you can use directly.

- `class.CollectionMetricPublisher`_ contain a collector of other publisher and will publish the metrics to all of them.
- `class.HostedGraphitePublisher`_ will publish the metrics to HostedGraphite.
- `class.BeberleiMetricsAdapterPublisher`_ will publish the metrics using a "collector" from the `Beberlei's metrics library <https://github.com/beberlei/metrics>`_.

In order to create your own collector, you will just have to implement the `interface.MetricPublisher`_ interface.

.. |class.CollectionMetricPublisher| replace:: ``class.CollectionMetricPublisher``
.. _class.CollectionMetricPublisher: https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/Metrics/Publisher/CollectionMetricPublisher.php
.. |class.HostedGraphitePublisher| replace:: ``class.HostedGraphitePublisher``
.. _class.HostedGraphitePublisher: https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/Metrics/Publisher/HostedGraphitePublisher.php
.. |class.BeberleiMetricsAdapterPublisher| replace:: ``class.BeberleiMetricsAdapterPublisher``
.. _class.BeberleiMetricsAdapterPublisher: https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/Metrics/Publisher/BeberleiMetricsAdapterPublisher.php
.. |interface.MetricPublisher| replace:: ``interface.MetricPublisher``
.. _interface.MetricPublisher: https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/Metrics/Publisher/MetricPublisher.php
