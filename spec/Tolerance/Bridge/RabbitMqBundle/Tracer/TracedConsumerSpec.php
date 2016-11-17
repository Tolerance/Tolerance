<?php

namespace spec\Tolerance\Bridge\RabbitMqBundle\Tracer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Tracer\Span\Identifier;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanFactory\Amqp\AmqpSpanFactory;
use Tolerance\Tracer\SpanStack\SpanStack;
use Tolerance\Tracer\Tracer;

class TracedConsumerSpec extends ObjectBehavior
{
    function let(ConsumerInterface $decoratedConsumer, Tracer $tracer, SpanStack $spanStack, AmqpSpanFactory $amqpSpanFactory)
    {
        $span = new Span(
            Identifier::fromString('1234'),
            'name',
            Identifier::fromString('1234')
        );

        $amqpSpanFactory->fromReceivedMessage(Argument::type(AMQPMessage::class))->willReturn($span);
        $amqpSpanFactory->fromConsumedMessage(Argument::type(AMQPMessage::class))->willReturn($span);

        $this->beConstructedWith($decoratedConsumer, $tracer, $spanStack, $amqpSpanFactory);
    }

    function it_is_a_consumer()
    {
        $this->shouldImplement(ConsumerInterface::class);
    }

    function it_returns_the_decorated_consumer_result(ConsumerInterface $decoratedConsumer)
    {
        $message = new AMQPMessage('');

        $decoratedConsumer->execute($message)->shouldBeCalled()->willReturn(4);

        $this->execute($message)->shouldReturn(4);
    }

    function it_traces_the_span(ConsumerInterface $decoratedConsumer, Tracer $tracer)
    {
        $tracer->trace(Argument::containing(Argument::type(Span::class)))->shouldBeCalled();

        $this->execute(new AMQPMessage(''));
    }

    function it_adds_the_span_in_the_stack_during_the_execution(ConsumerInterface $decoratedConsumer, SpanStack $spanStack)
    {
        $message = new AMQPMessage('');

        $spanStack->push(Argument::type(Span::class))->shouldBeCalled();
        $decoratedConsumer->execute($message)->shouldBeCalled();
        $spanStack->pop()->shouldBeCalled();

        $this->execute($message);
    }
}
