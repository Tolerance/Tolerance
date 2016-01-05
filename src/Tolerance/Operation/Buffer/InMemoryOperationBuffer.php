<?php

namespace Tolerance\Operation\Buffer;

use Tolerance\Operation\Operation;

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
    public function current()
    {
        if (false !== ($operation = current($this->operations))) {
            return $operation;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function pop()
    {
        return array_shift($this->operations);
    }
}
