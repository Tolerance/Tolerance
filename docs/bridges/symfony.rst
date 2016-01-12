Symfony Bridge
==============

The Symfony bridge is a bundle that you can use in any Symfony application.

.. code-block:: php

    $bundles[] = new Tolerance\Bridge\Symfony\Bundle\ToleranceBundle();

**Note**: you can also *manually* use the classes in the :code:`Tolerance\Bridge\Symfony` namespace if you do not want
to install this bundle in your Symfony application.

Request Identifier
------------------

The request identifier integration gives you:

- Service to access the request identifier resolver, generator and storage
- Request listener that reads the request identifier from a request's header
- Monolog processor that adds the request identifier to the context of each log
- Registered Guzzle middleware that adds the current request identifier if you are using `CsaGuzzleBundle <https://github.com/csarrazi/CsaGuzzleBundle>`_

You can enable the request identifier integration in the bundle configuration:

.. code-block:: yaml

    tolerance:
        request_identifier: ~

The bundle will then creates for you the following 3 services:

- :code:`tolerance.request_identifier.resolver` that contains the resolver
- :code:`tolerance.request_identifier.generator` that contains the generator
- :code:`tolerance.request_identifier.storage` that contains the storage

The bridge will also configure the request identifier listener to stores the request identifier automatically before
any of your business logic is called. That way, if the request contains your header, it won't generate a new request
identifier.

You can configure the header used in the configuration of the bundle, which is by default :code:`X-Request-Id`:

.. code-block:: yaml

    tolerance:
        request_identifier:
            header: X-Request-Id

By default, it also registers the Monolog processor but you can **disable** it with the following configuration:

.. code-block:: yaml

    tolerance:
        request_identifier:
            monolog: false

If you are using the `CsaGuzzleBundle <https://github.com/csarrazi/CsaGuzzleBundle>`_ (in its version >= 2.0) then the
`Guzzle middleware <request-identifier.html#guzzle-middleware>`_ is automatically registered thanks to a service
tagged :code:`csa_guzzle.middleware` and aliased :code:`tolerance_request_identifier`. If you want to disable it you can
use the following configuration:

.. code-block:: yaml

    tolerance:
        request_identifier:
            guzzle: false
