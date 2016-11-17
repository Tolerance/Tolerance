<?php

namespace spec\Tolerance\Bridge\RabbitMqBundle\Tracer;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Tracer\Span\Identifier;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanFactory\Amqp\AmqpSpanFactory;
use Tolerance\Tracer\Tracer;

class TracedProducerSpec extends ObjectBehavior
{
    function let(ProducerInterface $decoratedProducer, AmqpSpanFactory $amqpSpanFactory, Tracer $tracer)
    {
        $this->beConstructedWith($decoratedProducer, $amqpSpanFactory, $tracer);
    }

    function it_is_a_producer()
    {
        $this->shouldImplement(ProducerInterface::class);
    }

    function it_should_add_the_trace_headers(ProducerInterface $decoratedProducer, AmqpSpanFactory $amqpSpanFactory, Tracer $tracer)
    {
        $span = new Span(
            Identifier::fromString('1234'),
            'name',
            Identifier::fromString('trace')
        );

        $amqpSpanFactory->fromProducedMessage(Argument::type(AMQPMessage::class))->willReturn($span);

        $decoratedProducer->publish('', '', Argument::that(function(array $properties) {
            if (!array_key_exists('application_headers', $properties)) {
                return false;
            }

            $headers = $properties['application_headers']->getNativeData();

            return isset($headers['X-B3-SpanId']) && isset($headers['X-B3-TraceId']);
        }))->shouldBeCalled();

        $tracer->trace([$span])->shouldBeCalled();

        $this->publish('', '');
    }
}
