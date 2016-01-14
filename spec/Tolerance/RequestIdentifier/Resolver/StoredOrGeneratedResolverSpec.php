<?php

namespace spec\Tolerance\RequestIdentifier\Resolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\RequestIdentifier\Generator\RequestIdentifierGenerator;
use Tolerance\RequestIdentifier\RequestIdentifier;
use Tolerance\RequestIdentifier\Resolver\RequestIdentifierResolver;
use Tolerance\RequestIdentifier\Storage\RequestIdentifierStorage;

class StoredOrGeneratedResolverSpec extends ObjectBehavior
{
    function let(RequestIdentifierStorage $storage, RequestIdentifierGenerator $generator)
    {
        $this->beConstructedWith($storage, $generator);
    }

    function it_is_a_request_identifier_resolver()
    {
        $this->shouldImplement(RequestIdentifierResolver::class);
    }

    function it_returns_the_stored_identifier_if_found(RequestIdentifierStorage $storage, RequestIdentifier $identifier)
    {
        $storage->getRequestIdentifier()->willReturn($identifier);

        $this->resolve()->shouldReturn($identifier);
    }

    function it_generates_and_stores_the_identifier_if_nothing_in_storage(RequestIdentifierStorage $storage, RequestIdentifierGenerator $generator, RequestIdentifier $identifier)
    {
        $generator->generate()->willReturn($identifier);
        $storage->getRequestIdentifier()->willReturn(null);
        $storage->setRequestIdentifier($identifier)->shouldBeCalled();

        $this->resolve()->shouldReturn($identifier);
    }
}
