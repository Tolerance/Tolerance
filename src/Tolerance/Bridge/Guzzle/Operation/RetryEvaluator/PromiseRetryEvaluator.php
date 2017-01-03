<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Guzzle\Operation\RetryEvaluator;

use GuzzleHttp\Psr7\Response;
use Tolerance\Operation\RetryEvaluator\RetryEvaluator;

class PromiseRetryEvaluator implements RetryEvaluator
{
    /**
     * {@inheritdoc}
     */
    public function shouldRetry($result)
    {
        if ($result instanceof Response && $result->getStatusCode() < 500) {
            return false;
        }

        return true;
    }
}
