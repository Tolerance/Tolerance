<?php

namespace Tolerance;

use Tolerance\Waiter\WaiterException;

interface Waiter
{
    /**
     * @param int $seconds
     *
     * @throws WaiterException
     */
    public function wait($seconds);
}
