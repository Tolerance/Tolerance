<?php

namespace spec\Tolerance\MessageProfile\HttpRequest\Psr7\RequestIdentifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Tolerance\MessageProfile\HttpRequest\Psr7\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;

class HeaderRequestIdentifierResolverSpec extends ObjectBehavior
{
    function let(MessageIdentifierGenerator $messageIdentifierGenerator)
    {
        $this->beConstructedWith($messageIdentifierGenerator, 'X-Request-Id');
    }

    function it_is_a_psr7_request_identifier_resolver()
    {
        $this->shouldImplement(RequestIdentifierResolver::class);
    }

    function it_generate_an_identifier_is_no_header(MessageIdentifierGenerator $messageIdentifierGenerator, RequestInterface $request, MessageIdentifier $identifier)
    {
        $request->hasHeader('X-Request-Id')->willReturn(false);

        $messageIdentifierGenerator->generate()->willReturn($identifier);
        $this->resolve($request)->shouldReturn($identifier);
    }

    function it_uses_the_identifier_of_the_request(RequestInterface $request, MessageIdentifier $identifier)
    {
        $request->hasHeader('X-Request-Id')->willReturn(true);
        $request->getHeader('X-Request-Id')->willReturn(['1234']);

        $this->resolve($request)->shouldBeLike(StringMessageIdentifier::fromString('1234'));
    }
}
