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

use Tolerance\Operation\Callback;
use Tolerance\Operation\Exception\UnsupportedOperation;
use Tolerance\Operation\Operation;

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

        return $operation->call();
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $operation instanceof Callback;
    }
}
