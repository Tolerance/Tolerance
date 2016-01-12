<?php

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Operation;

class CallbackOperationRunner implements OperationRunner
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
