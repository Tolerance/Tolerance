<?php

namespace spec\Tolerance\Tracer\SpanFactory\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Tracer\Clock\Clock;
use Tolerance\Tracer\EndpointResolver\EndpointResolver;
use Tolerance\Tracer\IdentifierGenerator\IdentifierGenerator;
use Tolerance\Tracer\Span\Identifier;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanStack\SpanStack;

class AmqpSpanFactorySpec extends ObjectBehavior
{
    function let(IdentifierGenerator $identifierGenerator, Clock $clock, SpanStack $spanStack, EndpointResolver $endpointResolver)
    {
        $identifierGenerator->generate()->willReturn(Identifier::fromString('1234'));

        $this->beConstructedWith($identifierGenerator, $clock, $spanStack, $endpointResolver);
    }

    function it_generates_a_message_identifier_from_incoming_message_without_headers()
    {
        $span = $this->fromReceivedMessage(new AMQPMessage(''));
        $span->shouldHaveAnIdentifier();
    }

    function it_uses_the_information_from_headers_if_found()
    {
        $span = $this->fromReceivedMessage(new AMQPMessage('', [
            'application_headers' => [
                'X-B3-TraceId' => '1234',
                'X-B3-SpanId' => '9876',
            ]
        ]));

        $span->getIdentifier()->shouldBeLike(Identifier::fromString('9876'));
        $span->getTraceIdentifier()->shouldBeLike(Identifier::fromString('1234'));
    }

    function it_uses_the_name_in_headers_if_found()
    {
        $span = $this->fromReceivedMessage(new AMQPMessage('', [
            'application_headers' => [
                'name' => 'My-Command',
            ]
        ]));

        $span->getName()->shouldBeLike('My-Command');
    }

    public function getMatchers()
    {
        return [
            'haveAnIdentifier' => function(Span $span) {
                return null !== $span->getIdentifier();
            },
        ];
    }
}
