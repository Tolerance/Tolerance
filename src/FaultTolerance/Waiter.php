<?php

namespace FaultTolerance;

use FaultTolerance\Waiter\WaiterException;

interface Waiter
{
    /**
     * @param int $seconds
     *
     * @throws WaiterException
     */
    public function wait($seconds);
}
