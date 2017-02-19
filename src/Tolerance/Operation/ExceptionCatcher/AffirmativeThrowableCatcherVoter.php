<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\ExceptionCatcher;

class AffirmativeThrowableCatcherVoter implements ThrowableCatcherVoter
{
    /**
     * @var array|ThrowableCatcherVoter[]
     */
    private $voters;

    /**
     * @param ThrowableCatcherVoter[] $voters
     */
    public function __construct(array $voters)
    {
        $this->voters = $voters;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldCatch(\Exception $e)
    {
        return $this->shouldCatchThrowable($e);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldCatchThrowable($throwable)
    {
        foreach ($this->voters as $voter) {
            if ($voter->shouldCatchThrowable($throwable)) {
                return true;
            }
        }

        return false;
    }
}
