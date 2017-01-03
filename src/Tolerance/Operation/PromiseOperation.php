<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation;

class PromiseOperation implements Operation
{
    private $promiseProvider;

    public function __construct(callable $promiseProvider)
    {
        $this->promiseProvider = $promiseProvider;
    }

    public function getPromise()
    {
        $promise = call_user_func($this->promiseProvider);
        if (!is_object($promise) || !method_exists($promise, 'then')) {
            throw new \LogicException('The "promiseProvider" must return a promise with a "then" method');
        }

        return $promise;
    }
}
