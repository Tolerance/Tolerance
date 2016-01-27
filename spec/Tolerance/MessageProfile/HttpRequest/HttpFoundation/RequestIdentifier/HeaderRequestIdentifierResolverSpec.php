<?php

namespace spec\Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;

class HeaderRequestIdentifierResolverSpec extends ObjectBehavior
{
    function let(MessageIdentifierGenerator $generator)
    {
        $this->beConstructedWith($generator, 'X-Request-Id');
    }

    function it_is_a_request_identifier_resolver()
    {
        $this->shouldImplement(RequestIdentifierResolver::class);
    }

    function it_returns_the_request_identifier_from_the_request()
    {
        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_X-Request-Id' => '1234',
        ]);

        $this->resolve($request)->shouldBeLike(StringMessageIdentifier::fromString('1234'));
    }

    function it_generates_the_request_identifier_if_the_request_do_not_contains_the_identifier(MessageIdentifierGenerator $generator, MessageIdentifier $messageIdentifier)
    {
        $request = Request::create('/');

        $generator->generate()->shouldBeCalled()->willReturn($messageIdentifier);

        $this->resolve($request)->shouldReturn($messageIdentifier);
    }
}
