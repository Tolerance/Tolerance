Guzzle
======

The Symfony bundle comes with an integration for `Guzzle <http://docs.guzzlephp.org/en/latest/>`_ that allows automatic
retry of failed requests.

Configuration
-------------

First, you need to install `the CsaGuzzleBundle <https://github.com/csarrazi/CsaGuzzleBundle/>`_.

Then you must enabled the bridge using the `guzzle` key:

.. code-block:: yaml

    tolerance:
        guzzle: true

Finally, just add the `retries` option in the configuration of your client:

.. code-block:: yaml

    csa_guzzle:
        clients:
            my_client:
                config:
                    retries: 2
