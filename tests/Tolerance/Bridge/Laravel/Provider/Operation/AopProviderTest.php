<?php

namespace Tolerance\Bridge\Laravel\Provider\Operation;

use PHPUnit\IlluminateTestCase;

class AopProviderTest extends IlluminateTestCase
{
    public function test_services_are_registered_and_instantiables()
    {
        $this->assertServiceIsRegistered(
            'tolerance.operation_runner_registry',
            \Tolerance\Bridge\Symfony\Operation\OperationRunnerRegistry::class
        );

        $this->assertServiceIsRegistered(
            'tolerance.operation_runner_listeners.buffered_termination',
            \Tolerance\Bridge\Symfony\Operation\RunBufferedOperationsWhenTerminates::class
        );
    }
}
