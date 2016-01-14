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
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        $this->buffer->add($operation);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $this->runner->supports($operation);
    }

    /**
     * Runs the buffered operations and return them.
     *
     * @return Operation[]
     */
    public function runBufferedOperations()
    {
        $ranOperations = [];

        while (null !== ($operation = $this->buffer->current())) {
            $this->runner->run($operation);
            $this->buffer->pop();

            $ranOperations[] = $operation;
        }

        return $ranOperations;
    }
}
