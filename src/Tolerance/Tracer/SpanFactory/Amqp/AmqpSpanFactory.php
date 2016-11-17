<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\SpanFactory\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use Tolerance\Tracer\Clock\Clock;
use Tolerance\Tracer\EndpointResolver\EndpointResolver;
use Tolerance\Tracer\IdentifierGenerator\IdentifierGenerator;
use Tolerance\Tracer\Span\Annotation;
use Tolerance\Tracer\Span\Identifier;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanStack\SpanStack;

class AmqpSpanFactory
{
    /**
     * @var IdentifierGenerator
     */
    private $identifierGenerator;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var SpanStack
     */
    private $spanStack;

    /**
     * @var EndpointResolver
     */
    private $endpointResolver;

    /**
     * @param IdentifierGenerator $identifierGenerator
     * @param Clock               $clock
     * @param SpanStack           $spanStack
     * @param EndpointResolver    $endpointResolver
     */
    public function __construct(IdentifierGenerator $identifierGenerator, Clock $clock, SpanStack $spanStack, EndpointResolver $endpointResolver)
    {
        $this->identifierGenerator = $identifierGenerator;
        $this->clock = $clock;
        $this->spanStack = $spanStack;
        $this->endpointResolver = $endpointResolver;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return Span
     */
    public function fromProducedMessage(AMQPMessage $message)
    {
        $currentSpan = $this->spanStack->current();

        return new Span(
            $this->identifierGenerator->generate(),
            $this->getMessageName($message),
            null !== $currentSpan ? $currentSpan->getTraceIdentifier() : $this->identifierGenerator->generate(),
            [
                new Annotation(Annotation::CLIENT_SEND, $this->clock->microseconds(), $this->endpointResolver->resolve()),
            ],
            [],
            $currentSpan !== null ? $currentSpan->getIdentifier() : null,
            $currentSpan !== null ? $currentSpan->getDebug() : null,
            $this->clock->microseconds()
        );
    }

    /**
     * @param AMQPMessage $message
     *
     * @return Span
     */
    public function fromReceivedMessage(AMQPMessage $message)
    {
        return new Span(
            $this->getIdentifierOrGenerate($message, 'X-B3-SpanId'),
            $this->getMessageName($message),
            $this->getIdentifierOrGenerate($message, 'X-B3-TraceId'),
            [
                new Annotation(Annotation::SERVER_RECEIVE, $this->clock->microseconds(), $this->endpointResolver->resolve()),
            ],
            [],
            $this->getIdentifier($message, 'X-B3-ParentSpanId'),
            $this->getMessageHeader($message, 'X-B3-Flags') == '1'
        );
    }

    /**
     * @param AMQPMessage $message
     *
     * @return Span
     */
    public function fromConsumedMessage(AMQPMessage $message)
    {
        return new Span(
            $this->getIdentifierOrGenerate($message, 'X-B3-SpanId'),
            $this->getMessageName($message),
            $this->getIdentifierOrGenerate($message, 'X-B3-TraceId'),
            [
                new Annotation(Annotation::SERVER_SEND, $this->clock->microseconds(), $this->endpointResolver->resolve()),
            ],
            [],
            $this->getIdentifier($message, 'X-B3-ParentSpanId'),
            $this->getMessageHeader($message, 'X-B3-Flags') == '1'
        );
    }

    /**
     * @param AMQPMessage $message
     *
     * @return string
     */
    private function getMessageName(AMQPMessage $message)
    {
        if (null === ($name = $this->getMessageHeader($message, 'name'))) {
            $name = 'AMQP';
        }

        return $name;
    }

    /**
     * @param AMQPMessage $message
     * @param string      $header
     *
     * @return Identifier
     */
    private function getIdentifierOrGenerate(AMQPMessage $message, $header)
    {
        if (null === ($identifier = $this->getIdentifier($message, $header))) {
            $identifier = $this->identifierGenerator->generate();
        }

        return $identifier;
    }

    /**
     * @param AMQPMessage $message
     * @param string      $header
     *
     * @return null|Identifier
     */
    private function getIdentifier(AMQPMessage $message, $header)
    {
        if (null !== ($header = $this->getMessageHeader($message, $header))) {
            return Identifier::fromString($header);
        }

        return null;
    }

    /**
     * @param AMQPMessage $message
     * @param string      $header
     *
     * @return string|null
     */
    private function getMessageHeader(AMQPMessage $message, $header)
    {
        $headers = $message->has('application_headers') ? $message->get('application_headers') : [];

        return array_key_exists($header, $headers) ? $headers[$header] : null;
    }
}
