<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony;

final class Events
{
    /**
     * This event will be dispatched when a request ends, even if the request ends with an
     * exception.
     */
    const REQUEST_ENDS = 'tolerance.request_ends';
}
