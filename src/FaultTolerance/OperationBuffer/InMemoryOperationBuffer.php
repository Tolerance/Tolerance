<?php
namespace FaultTolerance\OperationBuffer;

use FaultTolerance\Operation;
use FaultTolerance\OperationBuffer;

class InMemoryOperationBuffer implements OperationBuffer
{
    /**
     * @var Operation[]
     */
    private $operations = [];

    /**
     * {@inheritdoc}
     */
    public function add(Operation $operation)
    {
        $this->operations[] = $operation;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return array_shift($this->operations);
    }
}
