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

/**
 * This interface have to be implemented by waiters that contains state that change
 * their behaviour.
 *
 * Whoever uses a same instance of the waiter many times should then call the `resetState`
 * method before a "usage loop".
 */
interface StatefulWaiter extends Waiter
{
    /**
     * Reset the state of the waiter to allow the reusability.
     *
     * @throws WaiterException
     */
    public function resetState();
}
