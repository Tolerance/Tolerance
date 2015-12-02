<?php

namespace FaultTolerance;

use FaultTolerance\Waiter\WaiterException;

interface WaitStrategy
{
    /**
     * Do wait the time expected by the strategy.
     *
     * @throws WaiterException
     */
    public function wait();
}
