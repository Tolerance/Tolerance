<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Tracer\EventListener\OnKernelTerminate;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Tolerance\Tracer\SpanFactory\HttpFoundation\HttpFoundationSpanFactory;
use Tolerance\Tracer\SpanStack\SpanStack;
use Tolerance\Tracer\Tracer;

class TraceOutgoingResponse
{
    /**
     * @var HttpFoundationSpanFactory
     */
    private $httpFoundationSpanFactory;

    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * @var SpanStack
     */
    private $spanStack;

    /**
     * @param HttpFoundationSpanFactory $httpFoundationSpanFactory
     * @param Tracer                    $tracer
     * @param SpanStack                 $spanStack
     */
    public function __construct(HttpFoundationSpanFactory $httpFoundationSpanFactory, Tracer $tracer, SpanStack $spanStack)
    {
        $this->httpFoundationSpanFactory = $httpFoundationSpanFactory;
        $this->tracer = $tracer;
        $this->spanStack = $spanStack;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($span = $this->spanStack->pop()) {
            $this->tracer->trace([
                $this->httpFoundationSpanFactory->fromOutgoingResponse(
                    $event->getResponse(),
                    $span
                ),
            ]);
        }
    }
}
