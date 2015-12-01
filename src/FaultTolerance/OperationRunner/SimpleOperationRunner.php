<?php

namespace FaultTolerance\OperationRunner;

use FaultTolerance\Operation;
use FaultTolerance\OperationRunner;

class SimpleOperationRunner implements OperationRunner
{
    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        return $operation->run();
    }
}
