<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\PhpHttp;

use Psr\Http\Message\ResponseInterface;
use Tolerance\Operation\Exception\PromiseException;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;

final class StatusCodeVoter implements ThrowableCatcherVoter
{
    private $statusCodes;

    public function __construct(array $statusCodes)
    {
        $this->statusCodes = $statusCodes;
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
        return !$throwable instanceof PromiseException
            || $throwable->isRejected()
            || !$throwable->getValue() instanceof ResponseInterface
            || in_array($throwable->getValue()->getStatusCode(), $this->statusCodes)
        ;
    }
}
