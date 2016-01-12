<?php

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Exception\UnsupportedOperation;
use Tolerance\Operation\Operation;

interface OperationRunner
{
    /**
     * Run the given operation.
     *
     * @param Operation $operation
     *
     * @throws UnsupportedOperation
     *
     * @return Operation
     */
    public function run(Operation $operation);
}
