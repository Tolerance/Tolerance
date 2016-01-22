<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\Rate;

interface Rate
{
    /**
     * Get the number of ticks represented by this rate.
     *
     * @return float
     */
    public function getTicks();
}
