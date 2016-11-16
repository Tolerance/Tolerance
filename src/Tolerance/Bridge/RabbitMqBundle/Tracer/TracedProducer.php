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

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Tolerance\Tracer\SpanFactory\Amqp\AmqpSpanFactory;
use Tolerance\Tracer\Tracer;

final class TracedProducer implements ProducerInterface
{
    /**
     * @var ProducerInterface
     */
    private $decoratedProducer;

    /**
     * @var AmqpSpanFactory
     */
    private $amqpSpanFactory;

    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * @param ProducerInterface $decoratedProducer
     * @param AmqpSpanFactory   $amqpSpanFactory
     * @param Tracer            $tracer
     */
    public function __construct(ProducerInterface $decoratedProducer, AmqpSpanFactory $amqpSpanFactory, Tracer $tracer)
    {
        $this->decoratedProducer = $decoratedProducer;
        $this->amqpSpanFactory = $amqpSpanFactory;
        $this->tracer = $tracer;
    }

    /**
     * {@inheritdoc}
     */
    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {
        $message = new AMQPMessage((string) $msgBody, array_merge($additionalProperties, ['routing_key' => $routingKey]));
        $span = $this->amqpSpanFactory->fromProducedMessage($message);

        $additionalProperties = array_merge([
            'application_headers' => [
                'X-B3-SpanId' => (string) $span->getIdentifier(),
                'X-B3-TraceId' => (string) $span->getTraceIdentifier(),
                'X-B3-ParentSpanId' => (string) $span->getParentIdentifier(),
                'X-B3-Flags' => $span->getDebug() ? '1' : '0',
            ],
        ], $additionalProperties);

        $result = $this->decoratedProducer->publish($msgBody, $routingKey, $additionalProperties);

        $this->tracer->trace([$span]);

        return $result;
    }
}
