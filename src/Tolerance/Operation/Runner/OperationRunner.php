<?php

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Operation;

interface OperationRunner
{
    /**
     * Run the given operation.
     *
     * @param Operation $operation
     *
     * @return Operation
     */
    public function run(Operation $operation);
}
