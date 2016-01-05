<?php

namespace Tolerance\WaitStrategy;

use Tolerance\WaitStrategy;

class Max implements WaitStrategy
{
    /**
     * @var WaitStrategy
     */
    private $waitStrategy;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param WaitStrategy $waitStrategy
     * @param int $limit
     */
    public function __construct(WaitStrategy $waitStrategy, $limit)
    {
        $this->waitStrategy = $waitStrategy;
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function wait()
    {
        if ($this->limit-- <= 0) {
            throw new MaxRetryException();
        }

        $this->waitStrategy->wait();
    }
}
