<?php

namespace spec\Tolerance\Bridge\Guzzle\MessageProfile;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;
use Tolerance\RequestIdentifier\Resolver\RequestIdentifierResolver;
use Tolerance\RequestIdentifier\StringRequestIdentifier;

class MessageIdentifierMiddlewareFactorySpec extends ObjectBehavior
{
    function let(MessageIdentifierGenerator $identifierGenerator)
    {
        $this->beConstructedWith($identifierGenerator, 'X-Request-Id');
    }

    function it_returns_a_middleware()
    {
        $this->create()->shouldBeCallable();
    }

    function it_adds_a_message_identifier_in_the_headers(MessageIdentifierGenerator $identifierGenerator, RequestInterface $request)
    {
        $identifierGenerator->generate()->willReturn(StringMessageIdentifier::fromString('1234'));

        $request->withAddedHeader('X-Request-Id', '1234')->shouldBeCalled();

        $middlewareFactory = $this->create();
        $middleware = $middlewareFactory(function() {});
        $middleware($request, []);
    }
}
