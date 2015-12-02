<?php

namespace FaultTolerance\Waiter;

use FaultTolerance\Waiter;

class SleepWaiter implements Waiter
{
    /**
     * {@inheritdoc}
     */
    public function wait($seconds)
    {
        usleep($seconds * 1000000);
    }
}
