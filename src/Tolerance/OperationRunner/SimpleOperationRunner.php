<?php

namespace Tolerance\OperationRunner;

use Tolerance\Operation;
use Tolerance\OperationRunner;

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
