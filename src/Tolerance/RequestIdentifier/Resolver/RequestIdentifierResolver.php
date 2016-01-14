<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\RequestIdentifier\Resolver;

use Tolerance\RequestIdentifier\RequestIdentifier;

interface RequestIdentifierResolver
{
    /**
     * Get the request identifier.
     *
     * @return RequestIdentifier
     */
    public function resolve();
}
