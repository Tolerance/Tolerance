<?php

namespace FaultTolerance\Waiter;

use FaultTolerance\Waiter;

class SleepWaiter implements Waiter
{
    /**
     * {@inheritdoc}
     */
    public function wait($milliSeconds)
    {
        usleep($milliSeconds * 1000);
    }
}
