<?php

namespace spec\Tolerance\Bridge\SimpleBus\RabbitMqBundleBridge\Tracer;

use PhpAmqpLib\Wire\AMQPTable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;
use Tolerance\Tracer\Span\Span;

class MessageClassNameAdditionalPropertiesResolverSpec extends ObjectBehavior
{
    function it_is_an_additional_properties_resolver()
    {
        $this->shouldImplement(AdditionalPropertiesResolver::class);
    }

    function it_adds_the_end_of_class_header()
    {
        $this->resolveAdditionalPropertiesFor(new FooSpan())->shouldBeLike([
            'application_headers' => new AMQPTable([
                'name' => 'FooSpan',
            ]),
        ]);
    }
}

class FooSpan
{}
