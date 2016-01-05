<?php

namespace Tolerance\Waiter;

interface Waiter
{
    /**
     * @param int $seconds
     *
     * @throws WaiterException
     */
    public function wait($seconds);
}
