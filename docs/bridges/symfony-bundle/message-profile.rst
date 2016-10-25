Message Profile
===============

.. important::

    The integration of this component is deprecated in favor of the `Tracer component integration <tracer.html>`_.

This integration is created to require you the less effort possible to use Tolerance's Message Profile component. Enable
it with the following configuration.

.. code-block:: yaml

    tolerance:
        message_profile:
            enabled: true
            storage:
                in_memory: true


By default, it'll activate the following integrations:

- Request listener that reads the request identifier from a request's header
- Monolog processor that adds the request identifier to the context of each log
- Registered Guzzle middleware that adds the current request identifier if you are using `CsaGuzzleBundle <https://github.com/csarrazi/CsaGuzzleBundle>`_

Current peer
------------

You can given any kind of key-value mapping informations about the current peer in the YAML configuration.

.. code-block:: yaml

    tolerance:
        message_profile:
            current_peer:
                service: "My service"
                environment: %kernel.environment%
                version: 12345

This will be used as the information of the peer running this application, and will then be stored in the storage you
chose.

Storage configuration
---------------------

You can configure both the neo4j and elastica storages from the YAML configuration, here's an example:

.. code-block:: yaml

    tolerance:
        message_profile:
            storage:
                neo4j: neo4j_client_service_id
                # or...
                elastica: elastica_type_service_id

As you can see, that's your responsibility to create the services either by ourself or using bundles such as the
`FOSElasticaBundle <https://github.com/FriendsOfSymfony/FOSElasticaBundle>`_ that will do it for you.

.. tip::

    Checkout the `example service configuration <https://github.com/Tolerance/ExampleSymfonyService/blob/e1edd76cf68214c1615eef130b80cfd230e588a0/app/config/services.yml#L2-L19>`_
    to have an example of service construction for the Neo4j client.
