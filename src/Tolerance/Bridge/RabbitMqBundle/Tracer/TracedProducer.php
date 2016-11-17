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
use PhpAmqpLib\Wire\AMQPTable;
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

        if (!array_key_exists('application_headers', $additionalProperties)) {
            $additionalProperties['application_headers'] = new AMQPTable();
        } elseif (!$additionalProperties['application_headers'] instanceof AMQPTable) {
            throw new \InvalidArgumentException('Your `application_headers` must be an `AMQPTable`');
        }

        $headers = $additionalProperties['application_headers'];
        $headers->set('X-B3-SpanId', (string) $span->getIdentifier());
        $headers->set('X-B3-TraceId', (string) $span->getTraceIdentifier());
        $headers->set('X-B3-ParentSpanId', (string) $span->getParentIdentifier());
        $headers->set('X-B3-Flags', $span->getDebug() ? '1' : '0');

        $result = $this->decoratedProducer->publish($msgBody, $routingKey, $additionalProperties);

        $this->tracer->trace([$span]);

        return $result;
    }

    /**
     * This method is added for compatibility reasons with a strange way of closing
     * RabbitMq connections.
     *
     * @see https://github.com/php-amqplib/RabbitMqBundle/pull/378
     */
    public function close()
    {
        return $this->decoratedProducer->close();
    }
}
