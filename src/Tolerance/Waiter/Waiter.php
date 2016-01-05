<?php

namespace Tolerance\Waiter;

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
