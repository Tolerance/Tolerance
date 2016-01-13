Symfony Bridge
==============

The Symfony bridge is a bundle that you can use in any Symfony application.

.. code-block:: php

    $bundles[] = new Tolerance\Bridge\Symfony\Bundle\ToleranceBundle();

**Note**: you can also *manually* use the classes in the :code:`Tolerance\Bridge\Symfony` namespace if you do not want
to install this bundle in your Symfony application.

Operation runner factory
------------------------

When using simple operation runners, you can create them using the YML configuration of the bundle. Each operation runner
have a name (:code:`default` in the following example). The created operation runner will be available via the service named
:code:`tolerance.operation_runner.default`.

.. code-block:: yaml

    tolerance:
        operation_runners:
            default:
                retry:
                    runner:
                        callback: ~

                    strategy:
                        max:
                            count: 10
                            strategy:
                                exponential:
                                    exponent: 1
                                    waiter: tolerance.waiter.null

In that example, that will create a operation runner that is the retry operation runner decorating a callable operation runner.
The following image represents the imbrication of the different runners.

.. image:: ../_static/runner-factory.png

.. note::

    This YML factory do not support recursive operation runner. That means that you can't use a chain runner inside
    another chain runner. If you need to create more complex operation runners, you should create your own service
    with a simple factory like `the one that was in the tests before this YML factory <https://github.com/sroze/Tolerance/blob/f95bb3ae6a5f331a8d0579a991438f68e28f66f9/tests/Tolerance/Bridge/Symfony/Bundle/AppBundle/Operation/ThirdPartyRunnerFactory.php>`_.

.. tip::

    If you just need to add a decorator on a created operation runner, simply uses `Symfony DIC decorates features. <http://symfony.com/doc/current/components/dependency_injection/advanced.html#decorating-services>`_

AOP
---

The purpose of this Symfony integration is to help you using operations and operation runners in an easy way. By using
the AOP features provided by the `JMSAopBundle <https://github.com/schmittjoh/JMSAopBundle>`_ you can wrap a Symfony service
in an operation runner by simply using a tag.

.. important::

    You need to first install `JMSAopBundle <https://github.com/schmittjoh/JMSAopBundle>`_ in order to be able
    to use this AOP integration.

By default this feature is not activated so you need to activate it manually:

.. code-block:: yaml

    tolerance:
        aop: ~

Let's say now that you've a service for this :code:`YourService` object that contains methods that are a bit risky and
needs to be wrapped into an operation runner:

.. code-block:: php

    namespace App;

    class YourService
    {
        public function getSomething()
        {
            // This method needs to be in an operation runner because it's
            // doing something risky such as an API call.
        }
    }

Once you've that, you can use the :code:`tolerance.operation_wrapper` tag to wrap the different calls to some of your
service's methods inside an operation runner.

.. code-block:: xml

    <?xml version="1.0" ?>

    <container xmlns="http://symfony.com/schema/dic/services"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="app.your_service" class="App\YourService">
                <tag name="tolerance.operation_wrapper"
                     methods="getSomething"
                     runner="tolerance.operation_runner.default" />
            </service>
        </services>
    </container>

The tag have 2 configuration options:

- :code:`methods`: a comma separated names of the methods you want to *proxy*
- :code:`runner`: the service name of the operation runner to use

And that's all, your calls to the method :code:`getSomething` of your service will be wrapper inside a callback operation
and run with the operation runner :code:`operation_runner.service_name`.

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

The test application
--------------------

In order to test the Symfony bridge we have an application that uses as much as possible the different features. You can
find the application in Tolerance's repository at 2 different places:

- :code:`features/symfony/app` contains the Kernel and the application configuration
- :code:`tests/Tolerance/Bridge/Symfony/Bundle/AppBundle` contains the *AppBundle* bundle.
