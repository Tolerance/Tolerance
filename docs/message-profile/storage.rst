Storage
=======

Once a message profile is generated, we need to store it somewhere. At the moment, there are 2 storage implementations:

- `ElasticSearch`_
- `Neo4j`_

ElasticSearch
-------------

This storage is using `Elastica <https://github.com/ruflin/Elastica>`_ to send the normalized message profiles in an
index.

.. code-block:: php

    use Tolerance\MessageProfile\Storage\Normalizer\SimpleProfileNormalizer;
    use Tolerance\MessageProfile\Storage\ElasticaStorage;

    $normalizer = new SimpleProfileNormalizer();
    $storage = new ElasticaStorage($normalizer, $elasticaType);

Neo4j
-----

An other implementation stores the profiles in `Neo4j <http://neo4j.com>`_,the well known graph database. One of the
great advantages is that it enable us to run powerful queries on the message relations. It is using `Graphaware's Neo4j client <https://github.com/graphaware/neo4j-php-client>`_
to communicate with the Neo4j instance.

.. code-block:: php

    use Tolerance\MessageProfile\Storage\Normalizer\SimpleProfileNormalizer;
    use Tolerance\MessageProfile\Storage\Neo4jStorage;

    $normalizer = new SimpleProfileNormalizer();
    $storage = new Neo4jStorage($client, $normalizer);

