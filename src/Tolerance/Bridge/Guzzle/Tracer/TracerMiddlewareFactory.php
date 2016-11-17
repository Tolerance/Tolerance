<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Guzzle\Tracer;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tolerance\Tracer\SpanFactory\Psr7\Psr7SpanFactory;
use Tolerance\Tracer\Tracer;

/**
 * This Guzzle middleware is to be used with Guzzle 6.x
 *
 * If you are using Guzzle 4 or 5, you should have a look to the TracerSubscriber class.
 *
 */
class TracerMiddlewareFactory
{
    /**
     * @var Psr7SpanFactory
     */
    private $psr7SpanFactory;

    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * @param Psr7SpanFactory $psr7SpanFactory
     * @param Tracer          $tracer
     */
    public function __construct(Psr7SpanFactory $psr7SpanFactory, Tracer $tracer)
    {
        $this->psr7SpanFactory = $psr7SpanFactory;
        $this->tracer = $tracer;
    }

    /**
     * @return callable
     */
    public function create()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                // Store outgoing trace
                $span = $this->psr7SpanFactory->fromOutgoingRequest($request);
                $this->tracer->trace([$span]);

                // Add outgoing headers
                $request = $request
                    ->withHeader('X-B3-SpanId', (string) $span->getIdentifier())
                    ->withHeader('X-B3-TraceId', (string) $span->getTraceIdentifier())
                    ->withHeader('X-B3-ParentSpanId', (string) $span->getParentIdentifier())
                    ->withHeader('X-B3-Flags', $span->getDebug() ? '1' : '0')
                ;

                return $handler($request, $options)->then(function (ResponseInterface $response) use ($span) {
                    $this->tracer->trace([
                        $this->psr7SpanFactory->fromIncomingResponse($span, $response),
                    ]);

                    return $response;
                }, function ($reason) use ($span) {
                    if ($reason instanceof RequestException) {
                        $this->tracer->trace([
                            $this->psr7SpanFactory->fromIncomingResponse($span, $reason->getResponse()),
                        ]);
                    }

                    throw $reason;
                });
            };
        };
    }
}
