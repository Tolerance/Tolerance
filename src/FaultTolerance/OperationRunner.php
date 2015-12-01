<?php

namespace FaultTolerance;

use FaultTolerance\Operation;

interface OperationRunner
{
    public function run(Operation $operation);
}
