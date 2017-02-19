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

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Tolerance\Operation\PromiseOperation;
use Tolerance\Operation\Runner\RetryPromiseOperationRunner;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;
use Tolerance\Waiter\Waiter;

final class RetryPlugin implements Plugin
{
    private $waiter;

    private $retryVoter;

    public function __construct(Waiter $waiter, ThrowableCatcherVoter $retryVoter = null)
    {
        $this->waiter = $waiter;
        $this->retryVoter = $retryVoter ?: new RequestServerErrorVoter();
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $runner = new RetryPromiseOperationRunner($this->waiter, $this->retryVoter, null, false);

        return $runner->run(new PromiseOperation(function () use ($next, $request) {
            return $next($request);
        }));
    }
}
