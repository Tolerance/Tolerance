<?php

namespace Tolerance\RequestIdentifier;

interface RequestIdentifier
{
    /**
     * Get the string representation of the request identifier.
     *
     * @return string
     */
    public function __toString();
}
