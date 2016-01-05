<?php

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Operation;
use Tolerance\Operation\Runner\OperationRunner;

class SimpleOperationRunner implements OperationRunner
{
    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        $operation->run();

        return $operation;
    }
}
