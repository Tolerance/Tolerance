<?php

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Operation;
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
     * @param OperationRunner          $runner
     * @param \Tolerance\Waiter\Waiter $waitStrategy
     */
    public function __construct(OperationRunner $runner, Waiter $waitStrategy)
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
            return $this->runner->run($operation);
        } catch (\Exception $e) {
            try {
                $this->waitStrategy->wait();
            } catch (WaiterException $waiterException) {
                throw $e;
            }

            return $this->run($operation);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $this->runner->supports($operation);
    }
}
