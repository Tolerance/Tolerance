<?php

namespace Tolerance\RequestIdentifier\Generator;

use Tolerance\RequestIdentifier\StringRequestIdentifier;

class UniqIdGenerator implements RequestIdentifierGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return StringRequestIdentifier::fromString(uniqid('ri'));
    }
}
