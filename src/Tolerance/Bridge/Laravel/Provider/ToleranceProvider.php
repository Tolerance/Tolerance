<?php

namespace Tolerance\Bridge\Laravel\Provider;

use Illuminate\Support\ServiceProvider;

final class ToleranceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(Operation\AopProvider::class);
        $this->app->register(Operation\ListenerProvider::class);

        $this->app->register(WaiterProvider::class);
    }
}
