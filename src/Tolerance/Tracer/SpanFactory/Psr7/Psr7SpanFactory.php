<?php

namespace Tolerance\Tracer\SpanFactory\Psr7;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tolerance\Tracer\Clock\Clock;
use Tolerance\Tracer\EndpointResolver\EndpointResolver;
use Tolerance\Tracer\IdentifierGenerator\IdentifierGenerator;
use Tolerance\Tracer\Span\Annotation;
use Tolerance\Tracer\Span\BinaryAnnotation;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanContext;

class Psr7SpanFactory
{
    /**
     * @var IdentifierGenerator
     */
    private $identifierGenerator;

    /**
     * @var SpanContext
     */
    private $spanContext;

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
     * @param Clock $clock
     * @param EndpointResolver $endpointResolver
     * @param SpanContext $spanContext
     */
    public function __construct(IdentifierGenerator $identifierGenerator, Clock $clock, EndpointResolver $endpointResolver, SpanContext $spanContext)
    {
        $this->identifierGenerator = $identifierGenerator;
        $this->clock = $clock;
        $this->endpointResolver = $endpointResolver;
        $this->spanContext = $spanContext;
    }

    /**
     * @param RequestInterface $request
     *
     * @return Span
     */
    public function fromOutgoingRequest(RequestInterface $request)
    {
        $currentSpan = $this->spanContext->getCurrentSpan();

        return new Span(
            $this->identifierGenerator->generate(),
            $this->getName($request),
            null !== $currentSpan ? $currentSpan->getTraceIdentifier() : $this->identifierGenerator->generate(),
            [
                new Annotation(Annotation::CLIENT_SEND, $this->clock->microseconds(), $this->endpointResolver->resolve()),
            ],
            [
                new BinaryAnnotation('http.host', $request->getUri()->getHost(), BinaryAnnotation::TYPE_STRING),
            ],
            $currentSpan !== null ? $currentSpan->getIdentifier() : null,
            $currentSpan !== null ? $currentSpan->getDebug() : null
        );
    }

    /**
     * @param ResponseInterface $response
     * @param Span $originalSpan
     *
     * @return Span
     */
    public function fromIncomingResponse(ResponseInterface $response, Span $originalSpan)
    {
        return new Span(
            $originalSpan->getIdentifier(),
            $originalSpan->getName(),
            $originalSpan->getTraceIdentifier(),
            [
                new Annotation(Annotation::CLIENT_RECEIVE, $this->clock->microseconds(), $this->endpointResolver->resolve()),
            ],
            [],
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
        return $request->getMethod() . ' ' . $request->getUri()->getPath();
    }
}
