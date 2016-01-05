<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\RequestIdentifier\Storage;

use Tolerance\RequestIdentifier\RequestIdentifier;

interface RequestIdentifierStorage
{
    /**
     * Get the current request identifier.
     *
     * @return RequestIdentifier
     */
    public function getRequestIdentifier();

    /**
     * Set the request identifier.
     *
     * @param \Tolerance\RequestIdentifier\RequestIdentifier $requestIdentifier
     */
    public function setRequestIdentifier(RequestIdentifier $requestIdentifier);
}
