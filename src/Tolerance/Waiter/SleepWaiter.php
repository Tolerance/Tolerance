<?php

namespace Tolerance\Waiter;

class SleepWaiter implements Waiter
{
    /**
     * {@inheritdoc}
     */
    public function wait($seconds = 1)
    {
        usleep($seconds * 1000000);
    }
}
