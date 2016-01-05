<?php

namespace Tolerance\Waiter;

use Tolerance\Waiter\Waiter;

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
