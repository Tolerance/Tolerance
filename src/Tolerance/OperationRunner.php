<?php

namespace Tolerance;

use Tolerance\Operation;

interface OperationRunner
{
    public function run(Operation $operation);
}
