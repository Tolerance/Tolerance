<?php

namespace spec\Tolerance\Bridge\Monolog\RequestIdentifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\RequestIdentifier\RequestIdentifier;
use Tolerance\RequestIdentifier\Resolver\RequestIdentifierResolver;

class RequestIdentifierProcessorSpec extends ObjectBehavior
{
    function let(RequestIdentifierResolver $requestIdentifierResolver)
    {
        $this->beConstructedWith($requestIdentifierResolver);
    }

    function it_adds_the_request_identifier_in_the_tags(RequestIdentifierResolver $requestIdentifierResolver, RequestIdentifier $identifier)
    {
        $record = [
            'context' => [],
        ];

        $identifier->__toString()->willReturn('1234');
        $requestIdentifierResolver->resolve()->willReturn($identifier);

        $this($record)->shouldReturn([
            'context' => [
                'tags' => [
                    'request-identifier' => '1234',
                ],
            ],
        ]);
    }
}
