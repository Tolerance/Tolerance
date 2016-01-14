<?php

namespace Tolerance\Waiter;

class NullWaiter implements Waiter
{
    /**
     * {@inheritdoc}
     */
    public function wait($seconds = 0)
    {
    }
}
