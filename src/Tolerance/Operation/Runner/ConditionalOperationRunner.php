<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Exception\UnsatisfiedCondition;
use Tolerance\Operation\Operation;

/**
 * @author Christophe Willemsen <christophe@graphaware.com>
 */
class ConditionalOperationRunner extends ChainOperationRunner
{
    /**
     * @var Operation[]
     */
    protected $conditionalOperations = [];

    /**
     * Add an operation to be executed before the <code>run()</code> method.
     * All operations given to the <code>addConditionalOperation</code> method
     * should return true for the actual <code>run()</code> method to be called.
     *
     * @param \Tolerance\Operation\Operation $operation
     */
    public function addConditionalOperation(Operation $operation)
    {
        $this->conditionalOperations[] = $operation;
    }

    /**
     * @param \Tolerance\Operation\Operation $operation
     *
     * @return mixed
     *
     * @throws \Tolerance\Operation\Exception\UnsatisfiedCondition
     * @throws \Tolerance\Operation\Exception\UnsupportedOperation
     */
    public function run(Operation $operation)
    {
        $this->checkConditions();
        return parent::run($operation);
    }


    /**
     * Checks that all given <code>conditionalOperation</code> return true.
     *
     * @return bool
     *
     * @throws \Tolerance\Operation\Exception\UnsatisfiedCondition
     */
    protected function checkConditions()
    {
        foreach ($this->conditionalOperations as $conditionalOperation) {
            if (true !== $this->run($conditionalOperation)) {
                throw new UnsatisfiedCondition(sprintf('The operation %s did not statisfy.', get_class($conditionalOperation)));
            }
        }

        return true;
    }

}