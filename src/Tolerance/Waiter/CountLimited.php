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

use Tolerance\Waiter\Exception\CountLimitReached;

class CountLimited implements Waiter, StatefulWaiter
{
    /**
     * @var Waiter
     */
    private $waiter;

    /**
     * @var int
     */
    private $initialLimit;

    /**
     * @var int
     */
    private $currentLimit;

    /**
     * @param Waiter $waiter
     * @param int    $initialLimit
     */
    public function __construct(Waiter $waiter, $initialLimit)
    {
        $this->waiter = $waiter;
        $this->initialLimit = $initialLimit;

        $this->resetState();
    }

    /**
     * {@inheritdoc}
     */
    public function wait($seconds = 0)
    {
        if ($this->currentLimit-- <= 0) {
            throw new CountLimitReached();
        }

        $this->waiter->wait($seconds);
    }

    /**
     * {@inheritdoc}
     */
    public function resetState()
    {
        $this->currentLimit = $this->initialLimit;

        if ($this->waiter instanceof StatefulWaiter) {
            $this->waiter->resetState();
        }
    }
}
