<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Operation;

use Tolerance\Operation\Runner\OperationRunner;

class OperationRunnerRegistry
{
    /**
     * @var OperationRunner[]
     */
    private $operationRunners = [];

    /**
     * @param string $className
     *
     * @return OperationRunner[]
     */
    public function findAllByClass($className)
    {
        return array_values(array_filter($this->operationRunners, function (OperationRunner $operationRunner) use ($className) {
            return get_class($operationRunner) == $className || is_subclass_of($operationRunner, $className);
        }));
    }

    /**
     * @param OperationRunner $operationRunner
     */
    public function registerOperationRunner(OperationRunner $operationRunner)
    {
        $this->operationRunners[] = $operationRunner;
    }
}
