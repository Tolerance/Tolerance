<?php

namespace FaultTolerance;

use FaultTolerance\Waiter\WaiterException;

interface Waiter
{
    /**
     * @param int $milliSeconds
     *
     * @throws WaiterException
     */
    public function wait($milliSeconds);
}
