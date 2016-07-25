<?php

namespace Tolerance\Bridge\Laravel\Provider;

use PHPUnit\IlluminateTestCase;

class WaiterProviderTest extends IlluminateTestCase
{
    public function test_services_are_registered_and_instantiables()
    {
        $this->assertServiceIsRegistered(
            'tolerance.waiter.null',
            \Tolerance\Waiter\NullWaiter::class
        );

        $this->assertServiceIsRegistered(
            'tolerance.waiter.sleep',
            \Tolerance\Waiter\SleepWaiter::class
        );
    }
}
