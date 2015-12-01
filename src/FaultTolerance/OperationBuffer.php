<?php

namespace FaultTolerance;

use FaultTolerance\Operation;

interface OperationBuffer
{
    /**
     * Add a new operation in the buffer.
     *
     * @param Operation $operation
     */
    public function add(Operation $operation);

    /**
     * Returns an operation if there's some in the buffer.
     *
     * @return Operation|null
     */
    public function get();
}
