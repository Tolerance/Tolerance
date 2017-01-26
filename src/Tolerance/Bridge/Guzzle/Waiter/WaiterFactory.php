<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Guzzle\Waiter;

use Tolerance\Waiter\CountLimited;
use Tolerance\Waiter\ExponentialBackOff;
use Tolerance\Waiter\SleepWaiter;

/**
 * Waiter Factory.
 */
class WaiterFactory
{
    private $defaultRetry;

    public function __construct($defaultRetry = 0)
    {
        $this->defaultRetry = (int) $defaultRetry;
    }

    public function __invoke($options)
    {
        return new CountLimited(
            new ExponentialBackOff(
                new SleepWaiter(),
                1
            ),
            isset($options['retry']) ? $options['retry'] : $this->defaultRetry
        );
    }
}
