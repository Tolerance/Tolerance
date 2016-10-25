<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Tracer\EventListener\OnKernelRequest;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tolerance\Tracer\SpanStack\SpanStack;
use Tolerance\Tracer\SpanFactory\HttpFoundation\HttpFoundationSpanFactory;
use Tolerance\Tracer\Tracer;

class CreateAndTraceIncomingSpan
{
    /**
     * @var SpanStack
     */
    private $stack;

    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * @var HttpFoundationSpanFactory
     */
    private $httpFoundationSpanFactory;

    /**
     * @param SpanStack                 $stack
     * @param Tracer                    $tracer
     * @param HttpFoundationSpanFactory $httpFoundationSpanFactory
     */
    public function __construct(SpanStack $stack, Tracer $tracer, HttpFoundationSpanFactory $httpFoundationSpanFactory)
    {
        $this->stack = $stack;
        $this->tracer = $tracer;
        $this->httpFoundationSpanFactory = $httpFoundationSpanFactory;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $span = $this->httpFoundationSpanFactory->fromIncomingRequest($event->getRequest());
        $this->stack->push($span);
        $this->tracer->trace([$span]);
    }
}
