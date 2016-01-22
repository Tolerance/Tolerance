<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\OperationRunner;

use Tolerance\Operation\Operation;

interface ThrottlingIdentifierStrategy
{
    /**
     * Returns the identifier used to throttle the operation.
     *
     * @param Operation $operation
     *
     * @return string
     */
    public function getOperationIdentifier(Operation $operation);
}
