<?php

namespace FaultTolerance\OperationRunner;

use FaultTolerance\Operation;
use FaultTolerance\OperationBuffer;
use FaultTolerance\OperationRunner;

class BufferedOperationRunner implements OperationRunner
{
    /**
     * @var OperationRunner
     */
    private $runner;

    /**
     * @var OperationBuffer
     */
    private $buffer;

    /**
     * @param OperationRunner $runner
     * @param OperationBuffer $buffer
     */
    public function __construct(OperationRunner $runner, OperationBuffer $buffer)
    {
        $this->runner = $runner;
        $this->buffer = $buffer;
    }

    /**
     * @param Operation $operation
     */
    public function run(Operation $operation)
    {
        $this->buffer->add($operation);

        while (null !== ($operation = $this->buffer->current())) {
            $this->runner->run($operation);
            $this->buffer->pop();
        }
    }
}
