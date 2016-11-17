Introduction
============

Tolerance is a PHP library that provides fault tolerance and microservices related tools in order to be able
to solve some of the problems introduced by microservices.

Why ?
-----

Software fails. Software communicates with other softwares. Software can be distributed or a set of services, which
makes it even more subject to faults and monitoring/tracing problems.

Tolerance helps to `run fault-tolerant operations <operation-runner/intro.html>`_, `throttle <throttling/intro.html>`_
(ie rate limiting) your outgoing or incoming messages, `track messages across services and protocols <tracer/intro.html>`_
and more.

Getting started
---------------

The recommended way is to use Composer to install the `tolerance/tolerance` package.

.. code-block:: bash

    $ composer require tolerance/tolerance


If you are using Symfony, then checkout the `Symfony Bundle <bridges/symfony-bundle/intro.html>`_. Else, you should have a look
to the different components.

- `Operation runners <operation-runner/intro.html>`_
- `Tracer <tracer/intro.html>`_
- `Metrics <metrics/intro.html>`_
- `Throttling <throttling/intro.html>`_

Contributing
------------

Everything is open-source and therefore use the `GitHub repository <https://github.com/Tolerance/Tolerance>`_ to open an issue
or a pull-request.
