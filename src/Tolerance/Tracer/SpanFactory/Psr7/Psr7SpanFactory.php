<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\SpanFactory\Psr7;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tolerance\Tracer\Clock\Clock;
use Tolerance\Tracer\EndpointResolver\EndpointResolver;
use Tolerance\Tracer\IdentifierGenerator\IdentifierGenerator;
use Tolerance\Tracer\Span\Annotation;
use Tolerance\Tracer\Span\BinaryAnnotation;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanStack\SpanStack;

class Psr7SpanFactory
{
    /**
     * @var IdentifierGenerator
     */
    private $identifierGenerator;

    /**
     * @var SpanStack
     */
    private $spanStack;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var EndpointResolver
     */
    private $endpointResolver;

    /**
     * @param IdentifierGenerator $identifierGenerator
     * @param Clock               $clock
     * @param EndpointResolver    $endpointResolver
     * @param SpanStack           $spanStack
     */
    public function __construct(IdentifierGenerator $identifierGenerator, Clock $clock, EndpointResolver $endpointResolver, SpanStack $spanStack)
    {
        $this->identifierGenerator = $identifierGenerator;
        $this->clock = $clock;
        $this->endpointResolver = $endpointResolver;
        $this->spanStack = $spanStack;
    }

    /**
     * @param RequestInterface $request
     *
     * @return Span
     */
    public function fromOutgoingRequest(RequestInterface $request)
    {
        $currentSpan = $this->spanStack->current();

        return new Span(
            $this->identifierGenerator->generate(),
            $this->getName($request),
            null !== $currentSpan ? $currentSpan->getTraceIdentifier() : $this->identifierGenerator->generate(),
            [
                new Annotation(Annotation::CLIENT_SEND, $this->clock->microseconds(), $this->endpointResolver->resolve()),
            ],
            [
                new BinaryAnnotation('http.host', $request->getUri()->getHost(), BinaryAnnotation::TYPE_STRING),
                new BinaryAnnotation('http.path', $request->getUri()->getPath(), BinaryAnnotation::TYPE_STRING),
                new BinaryAnnotation('http.method', $request->getMethod(), BinaryAnnotation::TYPE_STRING),
            ],
            $currentSpan !== null ? $currentSpan->getIdentifier() : null,
            $currentSpan !== null ? $currentSpan->getDebug() : null
        );
    }

    /**
     * @param ResponseInterface $response
     * @param Span              $originalSpan
     *
     * @return Span
     */
    public function fromIncomingResponse(Span $originalSpan, ResponseInterface $response = null)
    {
        return new Span(
            $originalSpan->getIdentifier(),
            $originalSpan->getName(),
            $originalSpan->getTraceIdentifier(),
            [
                new Annotation(Annotation::CLIENT_RECEIVE, $this->clock->microseconds(), $this->endpointResolver->resolve()),
            ],
            [
                new BinaryAnnotation('http.status', $response !== null ? $response->getStatusCode() : 0, BinaryAnnotation::TYPE_INTEGER_16),
            ],
            $originalSpan->getParentIdentifier(),
            $originalSpan->getDebug()
        );
    }

    /**
     * @param RequestInterface $request
     *
     * @return string
     */
    private function getName(RequestInterface $request)
    {
        return $request->getMethod().' '.$request->getUri()->getPath();
    }
}
