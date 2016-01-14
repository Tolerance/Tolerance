<?php

namespace Tolerance\Bridge\Symfony\Operation;

use Tolerance\Operation\Runner\OperationRunner;

class OperationRunnerRegistry
{
    /**
     * @var OperationRunner[]
     */
    private $operationRunners = [];

    /**
     * @param string $className
     *
     * @return OperationRunner[]
     */
    public function findAllByClass($className)
    {
        return array_values(array_filter($this->operationRunners, function (OperationRunner $operationRunner) use ($className) {
            return get_class($operationRunner) == $className || is_subclass_of($operationRunner, $className);
        }));
    }

    /**
     * @param OperationRunner $operationRunner
     */
    public function registerOperationRunner(OperationRunner $operationRunner)
    {
        $this->operationRunners[] = $operationRunner;
    }
}
