<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\SpanFactory\HttpFoundation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tolerance\Tracer\Clock\Clock;
use Tolerance\Tracer\EndpointResolver\EndpointResolver;
use Tolerance\Tracer\IdentifierGenerator\IdentifierGenerator;
use Tolerance\Tracer\Span\Annotation;
use Tolerance\Tracer\Span\BinaryAnnotation;
use Tolerance\Tracer\Span\Identifier;
use Tolerance\Tracer\Span\Span;

class HttpFoundationSpanFactory
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
     * @var EndpointResolver
     */
    private $endpointResolver;

    /**
     * @param IdentifierGenerator $identifierGenerator
     * @param Clock               $clock
     * @param EndpointResolver    $endpointResolver
     */
    public function __construct(IdentifierGenerator $identifierGenerator, Clock $clock, EndpointResolver $endpointResolver)
    {
        $this->identifierGenerator = $identifierGenerator;
        $this->clock = $clock;
        $this->endpointResolver = $endpointResolver;
    }

    /**
     * @param Request $request
     *
     * @return Span
     */
    public function fromIncomingRequest(Request $request)
    {
        return new Span(
            $this->getOrGenerateIdentifier($request, 'X-B3-SpanId'),
            $this->getName($request),
            $this->getOrGenerateIdentifier($request, 'X-B3-TraceId'),
            [
                new Annotation(Annotation::SERVER_RECEIVE, $this->clock->microseconds(), $this->endpointResolver->resolve()),
            ],
            [
                new BinaryAnnotation('http.host', $request->getHost(), BinaryAnnotation::TYPE_STRING),
                new BinaryAnnotation('http.scheme', $request->getScheme(), BinaryAnnotation::TYPE_STRING),
                new BinaryAnnotation('http.path', $request->getPathInfo(), BinaryAnnotation::TYPE_STRING),
            ],
            $this->getIdentifier($request, 'X-B3-ParentSpanId'),
            $request->headers->get('X-B3-Flags') == '1',
            $this->clock->microseconds()
        );
    }

    /**
     * @param Response $response
     * @param Span     $originalSpan
     *
     * @return Span
     */
    public function fromOutgoingResponse(Response $response, Span $originalSpan)
    {
        $timestamp = $this->clock->microseconds();

        return new Span(
            $originalSpan->getIdentifier(),
            $originalSpan->getName(),
            $originalSpan->getTraceIdentifier(),
            [
                new Annotation(Annotation::SERVER_SEND, $timestamp, $this->endpointResolver->resolve()),
            ],
            [
                new BinaryAnnotation('http.status', $response->getStatusCode(), BinaryAnnotation::TYPE_INTEGER_16),
            ],
            $originalSpan->getParentIdentifier(),
            $originalSpan->getDebug(),
            $originalSpan->getTimestamp(),
            $timestamp - $originalSpan->getTimestamp()
        );
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getName(Request $request)
    {
        return $request->getMethod().' '.$request->getPathInfo();
    }

    /**
     * @param Request $request
     * @param string  $header
     *
     * @return Identifier
     */
    private function getOrGenerateIdentifier(Request $request, $header)
    {
        if (null === ($identifier = $this->getIdentifier($request, $header))) {
            $identifier = $this->identifierGenerator->generate();
        }

        return $identifier;
    }

    /**
     * @param Request $request
     * @param string  $header
     *
     * @return null|Identifier
     */
    private function getIdentifier(Request $request, $header)
    {
        if (null !== ($value = $request->headers->get($header))) {
            return Identifier::fromString($value);
        }

        return null;
    }
}
