Throttling
==========

The principle of throttling a set of operation is to restrict the maximum number of these operations to run
in a given time frame.

For instance, we want to be able to run a maximum of 10 requests per seconds per client. That means that the operations
tagged as "coming from the client X" have to be throttled with a rate of 10 requests per seconds. It is important
to note that the time frame can also be unknown and you can use your own *ticks* to achieve a rate limitation for
concurrent processes for instance.

.. toctree::
    :maxdepth: 2

    rate
    waiters
    strategies
    integrations
