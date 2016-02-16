Operation runners
=================

Once you've created an `operation <operations.html>`_, you now have to run it using an operation runner. First of all,
there's a set of *raw* operation runners that know how to run the default operations:

- The `callback runner <raw-runners.html#callback-runner>`_ that is able to run callback operations.
- The `chain runner <raw-runners.html#chain-runner>`_ that is able to chain operation runners that supports different operation types.

In addition, there's a few useful operation runners that decorate an existing one to add extra *behaviour*:

- The `retry runner <behavioural-runners.html#retry-runner>`_ will retry the operation until it is successful or considered as failing too much.
- The `buffered runner  <behavioural-runners.html#buffered-runner>`_ will buffer operations until you decide the run them.

.. note::

    The `Throttling component <../throttling/intro.html>`_ also come with a `Rate Limited Operation Runner <../throttling/integrations.html#operation-runner>`_

.. toctree::
    :maxdepth: 2

    operations
    raw-runners
    behavioural-runners
    custom
