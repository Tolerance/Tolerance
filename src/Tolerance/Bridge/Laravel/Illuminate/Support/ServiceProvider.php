<?php

namespace Tolerance\Bridge\Laravel\Illuminate\Support;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * @internal
 */
abstract class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Helper to have a more concise service declaration.
     *
     * @param string        $id
     * @param string        $class        FQCN of the service
     * @param \Closure      $instantiator Closure returning the service instance
     * @param string[]|null $tags
     */
    final public function registerService($id, $class, \Closure $instantiator = null, array $tags = null)
    {
        if (null === $instantiator) {
            $instantiator = function () use ($class) {
                return new $class();
            };
        }
        $this->app->singleton($id, $instantiator);
        $this->app->bind($class, $id);

        if (null !== $tags) {
            $this->app->tag($id, $tags);
        }
    }
}
