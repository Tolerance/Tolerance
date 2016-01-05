<?php

namespace Tolerance\RequestIdentifier;

interface RequestIdentifier
{
    /**
     * Get the string representation of the request UUID.
     *
     * @return string
     */
    public function get();
}
