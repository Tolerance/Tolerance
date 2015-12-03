<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FaultTolerance\WaitStrategy;

use FaultTolerance\WaitStrategy;

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
