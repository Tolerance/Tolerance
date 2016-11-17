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

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use Tolerance\Bridge\Guzzle\Tracer\SpanFactory\GuzzleMessageSpanFactory;
use Tolerance\Tracer\Tracer;

/**
 * The tracer subscriber is meant to be used with Guzzle 4 and 5.
 *
 * If you are using Guzzle 6 (recommended), you should have a look to the TracerMiddleware.
 */
class TracerSubscriber implements SubscriberInterface
{
    /**
     * @var GuzzleMessageSpanFactory
     */
    private $guzzleMessageSpanFactory;

    /**
     * @var Tracer
     */
    private $tracer;

    public function __construct(GuzzleMessageSpanFactory $guzzleMessageSpanFactory, Tracer $tracer)
    {
        $this->guzzleMessageSpanFactory = $guzzleMessageSpanFactory;
        $this->tracer = $tracer;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            'before'   => ['onBefore'],
            'complete' => ['onComplete'],
            'error'    => ['onError']
        ];
    }

    /**
     * @param BeforeEvent $event
     */
    public function onBefore(BeforeEvent $event)
    {
        $request = $event->getRequest();
        $span = $this->guzzleMessageSpanFactory->fromOutgoingRequest($request);
        $this->tracer->trace([$span]);

        $request->addHeaders([
            'X-B3-SpanId' => (string) $span->getIdentifier(),
            'X-B3-TraceId' => (string) $span->getTraceIdentifier(),
            'X-B3-ParentSpanId' => (string) $span->getParentIdentifier(),
            'X-B3-Flags' => $span->getDebug() ? '1' : '0',
        ]);
    }

    /**
     * @param CompleteEvent $event
     */
    public function onComplete(CompleteEvent $event)
    {
        $this->onEnd($event->getRequest(), $event->getResponse());
    }

    /**
     * @param ErrorEvent $event
     */
    public function onError(ErrorEvent $event)
    {
        $this->onEnd($event->getRequest(), $event->getResponse());
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     */
    public function onEnd(RequestInterface $request, ResponseInterface $response = null)
    {
        try {
            $span = $this->guzzleMessageSpanFactory->fromIncomingResponse($request, $response);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $this->tracer->trace([
            $span,
        ]);
    }
}
