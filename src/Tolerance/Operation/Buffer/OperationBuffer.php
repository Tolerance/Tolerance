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
     * @return \Tolerance\Operation\Operation|null
     */
    public function current();

    /**
     * Pop the operation from the head of the buffer.
     *
     * @return Operation
     */
    public function pop();
}
