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

class ExponentialBackOff implements Waiter, StatefulWaiter
{
    /**
     * @var Waiter
     */
    private $waiter;

    /**
     * @var int
     */
    private $initialExponent;

    /**
     * @var int|null
     */
    private $currentExponent;

    /**
     * @param Waiter $waiter
     * @param int    $initialExponent
     */
    public function __construct(Waiter $waiter, $initialExponent)
    {
        $this->waiter = $waiter;
        $this->initialExponent = $initialExponent;

        $this->resetState();
    }

    /**
     * {@inheritdoc}
     */
    public function resetState()
    {
        $this->currentExponent = $this->initialExponent;
    }

    /**
     * {@inheritdoc}
     */
    public function wait($seconds = 0)
    {
        $time = $this->getNextTime($seconds);

        $this->waiter->wait($time);

        ++$this->currentExponent;
    }

    /**
     * Return the amount of time that will be waited the next `wait` call.
     *
     * @param int $seconds
     *
     * @return int
     */
    public function getNextTime($seconds = 0)
    {
        return $seconds + exp($this->currentExponent);
    }
}
