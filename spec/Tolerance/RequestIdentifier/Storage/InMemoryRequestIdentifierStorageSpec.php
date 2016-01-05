<?php

namespace spec\Tolerance\RequestIdentifier\Storage;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\RequestIdentifier\RequestIdentifier;
use Tolerance\RequestIdentifier\Storage\RequestIdentifierStorage;

class InMemoryRequestIdentifierStorageSpec extends ObjectBehavior
{
    function it_it_a_request_identifier_storage()
    {
        $this->shouldImplement(RequestIdentifierStorage::class);
    }

    function its_stores_the_identifier(RequestIdentifier $identifier)
    {
        $this->setRequestIdentifier($identifier);
        $this->getRequestIdentifier()->shouldReturn($identifier);
    }
}
