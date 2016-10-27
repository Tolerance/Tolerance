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
    const FIRST_CALL = true;
    const RECURSIVE_CALL = false;

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

    /** @var bool */
    private $isFirstCall;

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
        $this->isFirstCall = self::FIRST_CALL;

        if (!$this->exceptionCatcherVoter instanceof ThrowableCatcherVoter) {
            trigger_error(sprintf('%s is deprecated, you should implement %s instead', ExceptionCatcherVoter::class, ThrowableCatcherVoter::class), E_USER_DEPRECATED);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        if ($this->hasWaiterStrategyToBeReset()) {

            $this->waitStrategy->resetState();
        }

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

        $this->isFirstCall = self::RECURSIVE_CALL;

        return $this->run($operation);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $this->runner->supports($operation);
    }

    /**
     * @return bool
     */
    private function hasWaiterStrategyToBeReset()
    {
        return $this->waitStrategy instanceof StatefulWaiter
            && self::FIRST_CALL === $this->isFirstCall;
    }
}
