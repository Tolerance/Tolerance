<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation;

abstract class StateContainedOperation implements Operation
{
    const STATE_PENDING = -1;
    const STATE_SUCCESSFUL = 0;
    const STATE_FAILED = 1;

    /**
     * @var int
     */
    private $state = self::STATE_PENDING;

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->state === self::STATE_SUCCESSFUL;
    }

    /**
     * {@inheritdoc}
     */
    public function isFailed()
    {
        return $this->state === self::STATE_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRun()
    {
        return $this->state != self::STATE_PENDING;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}
