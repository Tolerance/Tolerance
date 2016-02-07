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

final class Linear implements Waiter
{
    /**
     * @var Waiter
     */
    private $waiter;

    /**
     * @var float
     */
    private $time;

    /**
     * @param Waiter $waiter
     * @param float  $time
     */
    public function __construct(Waiter $waiter, $time)
    {
        $this->waiter = $waiter;
        $this->time = $time;
    }

    /**
     * {@inheritdoc}
     */
    public function wait($seconds = 0)
    {
        $this->waiter->wait($this->time);
    }
}
