<?php

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Exception\UnsupportedOperation;
use Tolerance\Operation\Operation;

class ChainOperationRunner implements OperationRunner
{
    /**
     * @var array|OperationRunner[]
     */
    private $runners;

    /**
     * @param OperationRunner[] $runners
     */
    public function __construct(array $runners)
    {
        $this->runners = $runners;
    }

    /**
     * @param OperationRunner $runner
     */
    public function addOperationRunner(OperationRunner $runner)
    {
        $this->runners[] = $runner;
    }

    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        foreach ($this->runners as $runner) {
            if ($runner->supports($operation)) {
                return $runner->run($operation);
            }
        }

        throw new UnsupportedOperation(sprintf(
            'No operation runner in chain supports the operation %s',
            get_class($operation)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        foreach ($this->runners as $runner) {
            if ($runner->supports($operation)) {
                return true;
            }
        }

        return false;
    }
}
