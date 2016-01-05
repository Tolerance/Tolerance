<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\OperationRunner;

use Tolerance\Operation;
use Tolerance\OperationBuffer;
use Tolerance\OperationRunner;

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
