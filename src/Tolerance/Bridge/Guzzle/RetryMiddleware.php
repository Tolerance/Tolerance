<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Guzzle;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Tolerance\Bridge\Guzzle\Operation\ExceptionCatcher\RequestServerErrorVoter;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;
use Tolerance\Operation\PromiseOperation;
use Tolerance\Operation\Runner\RetryPromiseOperationRunner;

/**
 * Guzzle 6 middleware that retries failed requests.
 */
class RetryMiddleware
{
    /**
     * @var callable
     */
    private $nextHandler;

    /**
     * @var callable
     */
    private $waiterFactory;

    /**
     * @var ThrowableCatcherVoter
     */
    private $errorVoter;

    public function __construct(
        callable $nextHandler,
        callable $waiterFactory,
        ThrowableCatcherVoter $errorVoter = null
    ) {
        $this->nextHandler = $nextHandler;
        $this->waiterFactory = $waiterFactory;
        $this->errorVoter = $errorVoter ?: new RequestServerErrorVoter();
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $nextHandler = $this->nextHandler;
        $operation = new PromiseOperation(function () use ($nextHandler, $request, $options) {
            return $nextHandler($request, $options);
        });

        $waiterFactory = $this->waiterFactory;
        $runner = new RetryPromiseOperationRunner($waiterFactory($options), $this->errorVoter);

        return $runner->run($operation);
    }
}
