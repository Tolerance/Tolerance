<?php

namespace Tolerance\Bridge\Laravel\Provider;

use Illuminate\Support\ServiceProvider;

final class ToleranceProvider extends ServiceProvider
{
    /**
     * Performs post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Resources/config/tolerance.php' => config_path('courier.php'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->register(MessageProfile\Storage\BufferedProvider::class);

        $this->app->register(MessageProfile\GuzzleProvider::class);
        $this->app->register(MessageProfile\JmsSerializerProvider::class);
        $this->app->register(MessageProfile\ListenerProvider::class);
        $this->app->register(MessageProfile\MonologProvider::class);
        $this->app->register(MessageProfile\StorageProvider::class);

        $this->app->register(Operation\AopProvider::class);
        $this->app->register(Operation\ListenerProvider::class);

        $this->app->register(WaiterProvider::class);
    }
}
