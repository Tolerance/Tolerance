<?php

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Operation;
use Tolerance\Operation\Buffer\OperationBuffer;

class BufferedOperationRunner implements OperationRunner
{
    /**
     * @var OperationRunner
     */
    private $runner;

    /**
     * @var \Tolerance\Operation\Buffer\OperationBuffer
     */
    private $buffer;

    /**
     * @param OperationRunner                             $runner
     * @param \Tolerance\Operation\Buffer\OperationBuffer $buffer
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
