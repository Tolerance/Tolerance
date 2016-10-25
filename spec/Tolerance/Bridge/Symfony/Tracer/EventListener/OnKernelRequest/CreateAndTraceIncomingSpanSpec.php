<?php

namespace spec\Tolerance\Bridge\Symfony\Tracer\EventListener\OnKernelRequest;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanContext;
use Tolerance\Tracer\SpanFactory\HttpFoundation\HttpFoundationSpanFactory;
use Tolerance\Tracer\SpanStack\SpanStack;
use Tolerance\Tracer\Tracer;

class CreateAndTraceIncomingSpanSpec extends ObjectBehavior
{
    function let(SpanStack $spanStack, Tracer $tracer, HttpFoundationSpanFactory $httpFoundationSpanFactory)
    {
        $this->beConstructedWith($spanStack, $tracer, $httpFoundationSpanFactory);
    }

    function it_traces_and_add_a_span_to_the_stack(SpanStack $spanStack, Tracer $tracer, HttpFoundationSpanFactory $httpFoundationSpanFactory, GetResponseEvent $event, Span $span)
    {
        $request = Request::create('/');
        $event->getRequest()->willReturn($request);
        $event->isMasterRequest()->willReturn(true);

        $httpFoundationSpanFactory->fromIncomingRequest($request)->willReturn($span);
        $spanStack->push($span)->shouldBeCalled();
        $tracer->trace([$span])->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
