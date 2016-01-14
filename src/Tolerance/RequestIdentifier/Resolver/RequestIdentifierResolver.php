<?php

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
