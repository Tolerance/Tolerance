<?php

namespace Tolerance\Waiter;

use Tolerance\Waiter;

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
