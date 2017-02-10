<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\Runner;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use Tolerance\Operation\Exception\PromiseException;
use Tolerance\Operation\Exception\UnsupportedOperation;
use Tolerance\Operation\ExceptionCatcher\IgnoreExceptionVoter;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;
use Tolerance\Operation\ExceptionCatcher\WildcardExceptionVoter;
use Tolerance\Operation\Operation;
use Tolerance\Operation\PromiseOperation;
use Tolerance\Waiter\StatefulWaiter;
use Tolerance\Waiter\Waiter;
use Tolerance\Waiter\WaiterException;

class RetryPromiseOperationRunner implements OperationRunner
{
    /**
     * @var Waiter
     */
    private $waitStrategy;

    /**
     * @var ThrowableCatcherVoter
     */
    private $fulfilledVoter;

    /**
     * @var ThrowableCatcherVoter
     */
    private $rejectedVoter;

    /**
     * @var bool
     */
    private $async;

    /**
     * @param Waiter                     $waitStrategy
     * @param ThrowableCatcherVoter|null $fulfilledVoter
     * @param ThrowableCatcherVoter|null $rejectedVoter
     */
    public function __construct(Waiter $waitStrategy, ThrowableCatcherVoter $fulfilledVoter = null, ThrowableCatcherVoter $rejectedVoter = null, $async = true)
    {
        $this->waitStrategy = $waitStrategy;
        $this->fulfilledVoter = $fulfilledVoter ?: new IgnoreExceptionVoter();
        $this->rejectedVoter = $rejectedVoter ?: new WildcardExceptionVoter();
        $this->async = $async;
    }

    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        if (!$operation instanceof PromiseOperation) {
            throw new UnsupportedOperation(sprintf(
                'Got operation of type %s but expect %s',
                get_class($operation),
                PromiseOperation::class
            ));
        }

        if ($this->waitStrategy instanceof StatefulWaiter) {
            $this->waitStrategy->resetState();
        }

        return $this->runOperation($operation);
    }

    /**
     * @param PromiseOperation $operation
     *
     * @return mixed
     */
    private function runOperation(PromiseOperation $operation)
    {
        $promise = $operation->getPromise();

        return $promise->then(
            $this->onTerminate($operation, $this->fulfilledVoter, true, $promise),
            $this->onTerminate($operation, $this->rejectedVoter, false, $promise)
        );
    }

    private function onTerminate(PromiseOperation $operation, ThrowableCatcherVoter $voter, $fulfilled, $promise)
    {
        return function ($value) use ($operation, $voter, $fulfilled, $promise) {
            $exception = new PromiseException($value, $fulfilled);
            if (!$voter->shouldCatchThrowable($exception)) {
                return $value;
            }

            try {
                $this->waitStrategy->wait();
            } catch (WaiterException $waiterException) {
                // If it is a Guzzle Promise, use Guzzle's objects.
                if ($promise instanceof PromiseInterface) {
                    return $fulfilled ? new FulfilledPromise($value) : new RejectedPromise($value);
                }

                throw $exception;
            }

            if ($this->async) {
                return $this->runOperation($operation);
            }

            return $this->runOperation($operation)->wait();
        };
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $operation instanceof PromiseOperation;
    }
}
