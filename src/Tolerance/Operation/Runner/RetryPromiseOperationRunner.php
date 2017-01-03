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

use Tolerance\Operation\Exception\UnsupportedOperation;
use Tolerance\Operation\Operation;
use Tolerance\Operation\PromiseOperation;
use Tolerance\Operation\RetryEvaluator\AlwaysRetryEvaluator;
use Tolerance\Operation\RetryEvaluator\NeverRetryEvaluator;
use Tolerance\Operation\RetryEvaluator\RetryEvaluator;
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
     * @var RetryEvaluator
     */
    private $fulfilledEvaluator;
    /**
     * @var RetryEvaluator
     */
    private $rejectedEvaluator;

    /**
     * @param Waiter $waitStrategy
     * @param RetryEvaluator|null $fulfilledEvaluator
     * @param RetryEvaluator|null $rejectedEvaluator
     */
    public function __construct(Waiter $waitStrategy, RetryEvaluator $fulfilledEvaluator = null, RetryEvaluator $rejectedEvaluator = null)
    {
        $this->waitStrategy = $waitStrategy;
        $this->fulfilledEvaluator = $fulfilledEvaluator ?: new NeverRetryEvaluator();
        $this->rejectedEvaluator = $rejectedEvaluator ?: new AlwaysRetryEvaluator();
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
    public function runOperation(PromiseOperation $operation)
    {
        return $operation->getPromise()->then(
            $this->onTerminate($operation, $this->fulfilledEvaluator),
            $this->onTerminate($operation, $this->rejectedEvaluator)
        );
    }

    protected function onTerminate(PromiseOperation $operation, RetryEvaluator $evaluator)
    {
        return function ($result) use ($operation, $evaluator) {
            if (!$evaluator->shouldRetry($result)) {
                return $result;
            }

            try {
                $this->waitStrategy->wait();
            } catch (WaiterException $waiterException) {
                return $result;
            }

            return $this->runOperation($operation);
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
