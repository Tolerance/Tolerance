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

use Tolerance\Operation\ExceptionCatcher\ExceptionCatcherVoter;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;
use Tolerance\Operation\ExceptionCatcher\WildcardExceptionVoter;
use Tolerance\Operation\Operation;
use Tolerance\Waiter\StatefulWaiter;
use Tolerance\Waiter\WaiterException;
use Tolerance\Waiter\Waiter;

class RetryOperationRunner implements OperationRunner
{
    /**
     * @var OperationRunner
     */
    private $runner;

    /**
     * @var \Tolerance\Waiter\Waiter
     */
    private $waitStrategy;

    /**
     * @var ExceptionCatcherVoter
     */
    private $exceptionCatcherVoter;

    /**
     * @param OperationRunner          $runner
     * @param \Tolerance\Waiter\Waiter $waitStrategy
     * @param ExceptionCatcherVoter    $exceptionCatcherVoter
     *
     * @todo Replace ExceptionCatcherVoter typehint with ThrowableCatcherVoter
     */
    public function __construct(OperationRunner $runner, Waiter $waitStrategy, ExceptionCatcherVoter $exceptionCatcherVoter = null)
    {
        $this->runner = $runner;
        $this->waitStrategy = $waitStrategy;
        $this->exceptionCatcherVoter = $exceptionCatcherVoter ?: new WildcardExceptionVoter();

        if (!$this->exceptionCatcherVoter instanceof ThrowableCatcherVoter) {
            trigger_error(sprintf('%s is deprecated, you should implement %s instead', ExceptionCatcherVoter::class, ThrowableCatcherVoter::class), E_USER_DEPRECATED);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        if ($this->waitStrategy instanceof StatefulWaiter) {
            $this->waitStrategy->resetState();
        }

        return $this->runOperation($operation);
    }

    /**
     * @param Operation $operation
     *
     * @return mixed
     */
    private function runOperation(Operation $operation)
    {
        try {
            return $this->runner->run($operation);
        } catch (\Throwable $e) {
            // treated below
        } catch (\Exception $e) {
            // treated below
        }

        // @todo keep only inner if once ExceptionCatcher is gone
        if ($this->exceptionCatcherVoter instanceof ThrowableCatcherVoter) {
            if (!$this->exceptionCatcherVoter->shouldCatchThrowable($e)) {
                throw $e;
            }
        } elseif (!$this->exceptionCatcherVoter->shouldCatch($e)) {
            throw $e;
        }

        try {
            $this->waitStrategy->wait();
        } catch (WaiterException $waiterException) {
            throw $e;
        }

        return $this->runOperation($operation);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $this->runner->supports($operation);
    }
}
