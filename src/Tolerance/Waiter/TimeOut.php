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

class TimeOut implements Waiter, StatefulWaiter
{
    /**
     * @var Waiter
     */
    private $delegateWaiter;

    /**
     * @var integer
     */
    private $timeOut;

    /**
     * @var float
     */
    private $secondsEllapsed;

    /**
     * @var Waiter $delegateWaiter
     * @var integer $timeOut
     */
    public function __construct(Waiter $delegateWaiter, $timeOut)
    {
        $this->delegateWaiter = $delegateWaiter;
        $this->timeOut = $timeOut;
        $this->secondsEllapsed = 0.0;
    }

    /**
     * {@inheritdoc}
     */
    public function wait($seconds = 1)
    {
        $this->delegateWaiter->wait($seconds);
        $this->secondsEllapsed += $seconds;
        if ($this->timeOut <= $this->secondsEllapsed) {
            throw Exception\TimedOutExceeded::withValue($this->timeOut);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resetState()
    {
        $this->secondsEllapsed = 0;
    }
}
