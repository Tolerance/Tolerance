<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Waiter\Exception;

use Tolerance\Waiter\WaiterException;

class TimedOutExceeded extends WaiterException
{
    public static function withValue($seconds)
    {
        return new static(sprintf('Execution exceeded the timeout of "%s seconds"', $seconds));
    }
}
