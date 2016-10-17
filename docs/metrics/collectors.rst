Collectors
==========

Tolerance have built-in collectors that you can use directly.

- `class.CollectionMetricCollector`_ contain a collector of other collectors and will collect the metrics from all of them.
- `class.RabbitMqCollector`_ will grab metrics from the RabbitMq management API.

In order to create your own collector, you will just have to implement the `interface.MetricCollector`_ interface.

.. |class.CollectionMetricCollector| replace:: ``class.CollectionMetricCollector``
.. _class.CollectionMetricCollector: https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/Metrics/Collector/CollectionMetricCollector.php
.. |class.RabbitMqCollector| replace:: ``class.RabbitMqCollector``
.. _class.RabbitMqCollector: https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/Metrics/Collector/RabbitMq/RabbitMqCollector.php
.. |interface.MetricCollector| replace:: ``interface.MetricCollector``
.. _interface.MetricCollector: https://github.com/Tolerance/Tolerance/blob/master/src/Tolerance/Metrics/Collector/MetricCollector.php
