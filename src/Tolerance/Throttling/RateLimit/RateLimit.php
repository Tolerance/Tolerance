<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\RateLimit;

interface RateLimit
{
    /**
     * Returns true if the limit was reached.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasReachedLimit($identifier);

    /**
     * Returns the number of ticks before the limits will be available again.
     *
     * @return float
     */
    public function getTicksBeforeUnderLimit($identifier);

    /**
     * Add a tick.
     *
     * @param string $identifier
     */
    public function tick($identifier);
}
