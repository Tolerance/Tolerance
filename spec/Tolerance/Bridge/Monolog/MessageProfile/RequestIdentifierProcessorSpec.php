<?php

namespace spec\Tolerance\Bridge\Monolog\MessageProfile;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;

class RequestIdentifierProcessorSpec extends ObjectBehavior
{
    function let(RequestIdentifierResolver $requestIdentifierResolver, RequestStack $requestStack)
    {
        $this->beConstructedWith($requestIdentifierResolver, $requestStack);
    }

    function it_adds_the_request_identifier_in_the_tags(RequestIdentifierResolver $requestIdentifierResolver, RequestStack $requestStack)
    {
        $record = [
            'context' => [],
        ];

        $request = Request::create('/');
        $requestStack->getCurrentRequest()->willReturn($request);
        $requestIdentifierResolver->resolve($request)->willReturn(StringMessageIdentifier::fromString('1234'));

        $this($record)->shouldReturn([
            'context' => [
                'tags' => [
                    'request-identifier' => '1234',
                ],
            ],
        ]);
    }
}
