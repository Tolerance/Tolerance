<?php

namespace Tolerance\Operation\Runner;

use Tolerance\Operation\Callback;
use Tolerance\Operation\Exception\UnsupportedOperation;
use Tolerance\Operation\Operation;
use Tolerance\Operation\StateContainedOperation;

class CallbackOperationRunner implements OperationRunner
{
    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        if (!$operation instanceof Callback) {
            throw new UnsupportedOperation(sprintf(
                'Got operation of type %s but expect %s',
                get_class($operation),
                Callback::class
            ));
        }

        try {
            $operation->call();
            $operation->setState(StateContainedOperation::STATE_SUCCESSFUL);
        } catch (\Exception $e) {
            $operation->setState(StateContainedOperation::STATE_FAILED);

            throw $e;
        }

        return $operation;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $operation instanceof Callback;
    }
}
