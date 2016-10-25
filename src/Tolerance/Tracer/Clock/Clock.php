<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\Clock;

interface Clock
{
    /**
     * Returns the current time in micro-seconds.
     *
     * @return int
     */
    public function microseconds();
}
