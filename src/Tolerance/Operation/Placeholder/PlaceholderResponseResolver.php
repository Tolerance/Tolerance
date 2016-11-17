<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\Placeholder;

use Tolerance\Operation\Operation;

interface PlaceholderResponseResolver
{
    /**
     * Create the response for the given operation and exception.
     *
     * @param Operation $operation
     * @param \Exception|\Throwable $throwable
     *
     * @return mixed
     */
    public function createResponse(Operation $operation, $throwable);
}
