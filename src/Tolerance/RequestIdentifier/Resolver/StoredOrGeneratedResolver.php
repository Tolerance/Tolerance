<?php

namespace Tolerance\RequestIdentifier\Resolver;

use Tolerance\RequestIdentifier\Generator\RequestIdentifierGenerator;
use Tolerance\RequestIdentifier\Storage\RequestIdentifierStorage;

class StoredOrGeneratedResolver implements RequestIdentifierResolver
{
    /**
     * @var RequestIdentifierStorage
     */
    private $storage;

    /**
     * @var RequestIdentifierGenerator
     */
    private $generator;

    /**
     * @param RequestIdentifierStorage $storage
     * @param RequestIdentifierGenerator $generator
     */
    public function __construct(RequestIdentifierStorage $storage, RequestIdentifierGenerator $generator)
    {
        $this->storage = $storage;
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (null === ($identifier = $this->storage->getRequestIdentifier())) {
            $identifier = $this->generator->generate();

            $this->storage->setRequestIdentifier($identifier);
        }

        return $identifier;
    }
}
