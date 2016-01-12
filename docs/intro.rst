Introduction
============

Tolerance is a PHP library that provides fault tolerance and microservices related tools in order to be able
to solve some of the problems introduced by microservices.

Fault tolerance
---------------

When running complex and large architectures, it is inevitable that some faults will arrive, whatever the hosting
infrastructure you are relying on.

Tolerance introduces a notion of operations that can be run by some operation runners that supports to automatically
retry these operations, buffer them or fallback them to a controlled error.

Monitoring
----------

Many services talking each other means an extremely hard investigation task when something goes wrong, or simply
when you have to have an idea of the different calls implied by one "primary call".

The request identifier component helps to keep a trace of the request and the Symfony, Guzzle, Monolog and other
bridges enhance the simplicity of using it in your application.

Installation
------------

The recommended way is to use Composer to install the `sroze/tolerance` package.

.. code-block:: bash

    $ composer require sroze/tolerance

