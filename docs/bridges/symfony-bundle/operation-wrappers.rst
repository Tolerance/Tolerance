Operation Wrappers
==================

The purpose of this Symfony integration is to help you using operations and operation runners in an easy way. By using
the AOP features provided by the `JMSAopBundle <https://github.com/schmittjoh/JMSAopBundle>`_ you can wrap a Symfony service
in an operation runner by simply using a tag or a YAML configuration.

.. important::

    You need to first install `JMSAopBundle <https://github.com/schmittjoh/JMSAopBundle>`_ in order to be able
    to use this AOP integration.

By default this feature is not activated so you need to activate it manually:

.. code-block:: yaml

    tolerance:
        aop:
            enabled: true

Using a tag
-----------

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

Using YAML
----------

You can wrap some methods of a given class into a given operation runner. The following example shows how simple it
can be to simply get metrics from some API calls for instance.

All the calls to the methods :code:`requestSomething` and :code:`findSomethingElse` to a service with the class
:code:`HttpThirdPartyClient` will be proxied through the operation runner :code:`tolerance.operation_runners.3rdparty`.
This `metrics operation runner <../../metrics/operation-runners.html>`_ created in YAML will record the success and failure of the
operations to a `metric publisher <../metrics/publishers.html>`_.

.. code-block:: yaml

    tolerance:
    aop:
        enabled: true

        wrappers:
            - class: "Acme\Your\HttpThirdPartyClient"
              methods:
                  - requestSomething
                  - findSomethingElse
              runner: tolerance.operation_runners.3rdparty

    operation_runners:
        default:
            callback: ~

        3rdparty:
            success_failure_metrics:
                publisher: tolerance.metrics.publisher.statsd
                namespace: 3rdparty.outgoing.requests
