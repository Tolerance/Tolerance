Operation runners
=================

This component aims to run atomic tasks (called *operations*) by using different operation runners. They can
`retry <behavioural-runners.html#retry-runner>`_ in case of a temporary fault, `buffer the operations <behavioural-runners.html#buffered-runner>`_,
fallback them with a default result, `rate limit <../throttling/integrations.html#operation-runner>`_ the throughput of operations and more.

.. toctree::
    :maxdepth: 2

    operations
    raw-runners
    behavioural-runners
    custom
