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

class PhpClock implements Clock
{
    /**
     * {@inheritdoc}
     */
    public function microseconds()
    {
        return (int) (microtime(true) * 1000 * 1000);
    }
}
