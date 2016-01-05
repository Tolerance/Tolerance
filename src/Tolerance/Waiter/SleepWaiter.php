<?php

namespace Tolerance\Waiter;

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
