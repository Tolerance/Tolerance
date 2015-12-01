<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FaultTolerance\OperationRunner;

use FaultTolerance\Operation;
use FaultTolerance\OperationRunner;

class SimpleOperationRunner implements OperationRunner
{
    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        return $operation->run();
    }
}
