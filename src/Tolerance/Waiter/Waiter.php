<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Waiter;

interface Waiter
{
    /**
     * Wait that given amount of time.
     *
     * @param float $seconds
     *
     * @throws WaiterException
     */
    public function wait($seconds = 0);
}
