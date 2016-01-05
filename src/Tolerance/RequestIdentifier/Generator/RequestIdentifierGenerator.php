<?php

namespace Tolerance\RequestIdentifier\Generator;

use Tolerance\RequestIdentifier\RequestIdentifier;

interface RequestIdentifierGenerator
{
    /**
     * Returns a new request identifier.
     *
     * @return RequestIdentifier
     */
    public function generate();
}
