<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\RetryEvaluator;

interface RetryEvaluator
{
    /**
     * Evaluate the result of an operation to determine if it should be retried.
     *
     * @param $result
     *
     * @return bool
     */
    public function shouldRetry($result);
}
