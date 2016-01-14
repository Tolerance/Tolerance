<?php

namespace spec\Tolerance\Bridge\Guzzle\RequestIdentifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Tolerance\RequestIdentifier\Resolver\RequestIdentifierResolver;
use Tolerance\RequestIdentifier\StringRequestIdentifier;

class MiddlewareFactorySpec extends ObjectBehavior
{
    function let(RequestIdentifierResolver $resolver)
    {
        $this->beConstructedWith($resolver, 'X-Request-Id');
    }

    function it_returns_a_middleware()
    {
        $this->create()->shouldBeCallable();
    }

    function its_middleware_adds_the_header_to_the_request(RequestIdentifierResolver $resolver, RequestInterface $request)
    {
        $identifier = StringRequestIdentifier::fromString('1234');
        $resolver->resolve()->shouldBeCalled()->willReturn($identifier);

        $request->withAddedHeader('X-Request-Id', '1234')->shouldBeCalled();

        $middlewareFactory = $this->create();
        $middleware = $middlewareFactory(function() {});
        $middleware($request, []);
    }
}
