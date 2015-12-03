<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FaultTolerance\OperationRunner;

use FaultTolerance\Operation;
use FaultTolerance\OperationRunner;
use FaultTolerance\Waiter\WaiterException;
use FaultTolerance\WaitStrategy;

class RetryOperationRunner implements OperationRunner
{
    /**
     * @var OperationRunner
     */
    private $runner;

    /**
     * @var WaitStrategy
     */
    private $waitStrategy;

    /**
     * @param OperationRunner $runner
     * @param WaitStrategy $waitStrategy
     */
    public function __construct(OperationRunner $runner, WaitStrategy $waitStrategy)
    {
        $this->runner = $runner;
        $this->waitStrategy = $waitStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        try {
            $this->runner->run($operation);
        } catch (\Exception $e) {
            try {
                $this->waitStrategy->wait();
            } catch (WaiterException $waiterException) {
                throw $e;
            }

            $this->run($operation);
        }
    }
}
