<?php

namespace Tolerance\Waiter;

interface Waiter
{
    /**
     * Wait that given amount of time.
     *
     * @param int $seconds
     *
     * @throws WaiterException
     */
    public function wait($seconds = 0);
}
