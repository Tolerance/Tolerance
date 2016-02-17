Rate
====

Even if you may not need to extend these main objects of the Throttling, here are the description of the :code:`Rate`
and :code:`RateMeasure` objects that are used by the rate limit implementations.

Rate
----

The :code:`Rate` interface simply defines a :code:`getTicks()` method that should returns a number. The first implementation is the
:code:`TimeRate` that defines a number of operation in a given time range.

.. code-block:: php

    use Tolerance\Throttling\Rate\TimeRate;

    $rate = new TimeRate(60, TimeRate::PER_SECOND)
    $rate = new TimeRate(1, TimeRate::PER_MINUTE)

The second implementation is the :code:`CounterRate` that simply defines a counter. This is mainly used to store a
counter such as in the internals of the `Leaky Bucket <strategies.html#leaky-bucket>`_ implementation or when you'll want to setup a rate limitation
for parallel running processes for instance.

Rate measure
------------

The :code:`RateMeasure` is mainly used in the internals to store a given :code:`Rate` at a given time. The only
implementation at the moment is the :code:`ImmutableRateMeasure`.

Storage
-------

What you have to care about is the storage of these rate measures because they need to be stored in order to ensure
the coherency or this rate limits, especially when running with concurrent requests.

In memory storage
~~~~~~~~~~~~~~~~~

The easiest way to start is to store the rate measures in memory. The major drawback is that in order to ensure your
rate limitation you need to have your application running in a single long-running script.

.. code-block:: php

    use Tolerance\Throttling\RateMeasureStorage\InMemoryStorage;

    $storage = new InMemoryStorage();
