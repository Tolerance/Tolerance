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
     * @param string        $class   FQCN of the service
     * @param \Closure      $closure Closure returning the service instance
     * @param string[]|null $tags
     */
    final public function registerService($id, $class, \Closure $closure, array $tags = null)
    {
        $this->app->bind($id, $closure);
        $this->app->bind($class, $id);

        if (null !== $tags) {
            $this->app->tag($id, $tags);
        }
    }
}
