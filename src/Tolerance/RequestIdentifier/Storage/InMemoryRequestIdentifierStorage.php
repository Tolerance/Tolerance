<?php

namespace Tolerance\RequestIdentifier\Storage;

use Tolerance\RequestIdentifier\RequestIdentifier;

class InMemoryRequestIdentifierStorage implements RequestIdentifierStorage
{
    /**
     * @var RequestIdentifier|null
     */
    private $identifier;

    /**
     * {@inheritdoc}
     */
    public function setRequestIdentifier(RequestIdentifier $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestIdentifier()
    {
        return $this->identifier;
    }
}
