<?php

namespace Tolerance\Bridge\Laravel\Provider;

use Tolerance\Bridge\Laravel\Illuminate\Support\ServiceProvider;

final class WaiterProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerService(
            'tolerance.waiter.null',
            \Tolerance\Waiter\NullWaiter::class
        );

        $this->registerService(
            'tolerance.waiter.sleep',
            \Tolerance\Waiter\SleepWaiter::class
        );
    }
}
