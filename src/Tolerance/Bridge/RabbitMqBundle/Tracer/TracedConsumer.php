<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\RabbitMqBundle\Tracer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Tolerance\Tracer\SpanFactory\Amqp\AmqpSpanFactory;
use Tolerance\Tracer\SpanStack\SpanStack;
use Tolerance\Tracer\Tracer;

final class TracedConsumer implements ConsumerInterface
{
    /**
     * @var ConsumerInterface
     */
    private $decoratedConsumer;

    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * @var SpanStack
     */
    private $spanStack;

    /**
     * @var AmqpSpanFactory
     */
    private $amqpSpanFactory;

    /**
     * @param ConsumerInterface $decoratedConsumer
     * @param Tracer            $tracer
     * @param SpanStack         $spanStack
     * @param AmqpSpanFactory   $amqpSpanFactory
     */
    public function __construct(ConsumerInterface $decoratedConsumer, Tracer $tracer, SpanStack $spanStack, AmqpSpanFactory $amqpSpanFactory)
    {
        $this->decoratedConsumer = $decoratedConsumer;
        $this->tracer = $tracer;
        $this->spanStack = $spanStack;
        $this->amqpSpanFactory = $amqpSpanFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(AMQPMessage $msg)
    {
        $span = $this->amqpSpanFactory->fromReceivedMessage($msg);

        $this->tracer->trace([$span]);

        $this->spanStack->push($span);

        $result = $this->decoratedConsumer->execute($msg);

        $this->tracer->trace([
            $this->amqpSpanFactory->fromConsumedMessage($msg),
        ]);

        $this->spanStack->pop();

        return $result;
    }
}
