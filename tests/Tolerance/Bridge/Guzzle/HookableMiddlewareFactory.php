<?php

namespace Tolerance\Bridge\Guzzle;

use Psr\Http\Message\RequestInterface;

class HookableMiddlewareFactory
{
    /**
     * @var callable
     */
    private $hook = null;

    /**
     * @return callable
     */
    public function create()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (null === $this->hook) {
                    return $handler($request, $options);
                }

                $hook = $this->hook;
                return $hook($request, $options);
            };
        };
    }

    /**
     * @param callable $hook
     */
    public function setHook(callable $hook)
    {
        $this->hook = $hook;
    }
}
