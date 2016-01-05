<?php

namespace Tolerance;

use Tolerance\Operation;

interface OperationBuffer
{
    /**
     * Add a new operation in the buffer.
     *
     * @param Operation $operation
     */
    public function add(Operation $operation);

    /**
     * Returns the current operation at the head of the buffer.
     *
     * @return Operation|null
     */
    public function current();

    /**
     * Pop the operation from the head of the buffer.
     *
     * @return Operation
     */
    public function pop();
}
