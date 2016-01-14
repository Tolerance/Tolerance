<?php

namespace Tolerance\Waiter;

use Tolerance\Waiter\Exception\CountLimitReached;

class CountLimited implements Waiter
{
    /**
     * @var Waiter
     */
    private $waiter;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param Waiter $waiter
     * @param int    $limit
     */
    public function __construct(Waiter $waiter, $limit)
    {
        $this->waiter = $waiter;
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function wait($seconds = 0)
    {
        if ($this->limit-- <= 0) {
            throw new CountLimitReached();
        }

        $this->waiter->wait($seconds);
    }
}
