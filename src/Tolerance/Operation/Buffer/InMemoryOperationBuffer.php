<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
