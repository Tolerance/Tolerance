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
        $this->app->bind(
            'tolerance.waiter.null',
            \Tolerance\Waiter\NullWaiter::class
        );

        $this->app->bind(
            'tolerance.waiter.sleep',
            \Tolerance\Waiter\SleepWaiter::class
        );
    }
}
