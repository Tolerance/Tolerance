Integrations
============

Once you've chosen your rate limit strategy you can either use it directly or integrates it with some of the
existing components of Tolerance.

- `Operation Runner`_ is an :code:`Operation Runner` that will run your operations based on the rate limit.

Operation Runner
----------------

The Rate Limited Operation Runner is the integration of rate limiting with operation runners. That way you can ensure
that all the operations you want to run will actually run at the given time rate.

.. code-block:: php

    $rateLimit = /* The implementation you wants */;

    $operationRunner = new RateLimitedOperationRunner(
        new SimpleOperationRunner(),
        $rateLimit,
        new SleepWaiter()
    );

    $operationRunner->run($operation);

By default, the identifier given to the rate limit is an empty string. The *optional* fourth parameter is an
object implementing the :code:`ThrottlingIdentifierStrategy` interface that will returns the identifier of the operation.

.. code-block:: php

    class ThrottlingIdentifierStrategy implements ThrottlingIdentifierStrategy
    {
        /**
         * {@inheritdoc}
         */
        public function getOperationIdentifier(Operation $operation)
        {
            if ($operation instanceof MyClientOperation) {
                return sprintf(
                    'client-%s',
                    $operation->getClient()->getIdentifier()
                );
            }

            return 'unknown-client';
        }
    }
