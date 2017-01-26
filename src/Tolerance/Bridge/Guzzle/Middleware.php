<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Guzzle;

use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;

class Middleware
{
    public static function retry(callable $waiterFactory, ThrowableCatcherVoter $errorVoter = null)
    {
        return function (callable $handler) use ($waiterFactory, $errorVoter) {
            return new RetryMiddleware($handler, $waiterFactory, $errorVoter);
        };
    }
}
