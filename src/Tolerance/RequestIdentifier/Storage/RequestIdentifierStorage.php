<?php

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
