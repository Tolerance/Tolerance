<?php

namespace spec\Tolerance\Bridge\Symfony\Tracer\EventListener\OnKernelTerminate;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanFactory\HttpFoundation\HttpFoundationSpanFactory;
use Tolerance\Tracer\SpanStack\SpanStack;
use Tolerance\Tracer\Tracer;

class TraceOutgoingResponseSpec extends ObjectBehavior
{
    function let(HttpFoundationSpanFactory $httpFoundationSpanFactory, Tracer $tracer, SpanStack $spanStack)
    {
        $this->beConstructedWith($httpFoundationSpanFactory, $tracer, $spanStack);
    }

    function it_traces_the_response_and_pop_the_stack(HttpFoundationSpanFactory $httpFoundationSpanFactory, Tracer $tracer, SpanStack $spanStack, Span $span, PostResponseEvent $event)
    {
        $response = new Response();
        $event->isMasterRequest()->willReturn(true);
        $event->getResponse()->willReturn($response);

        $spanStack->pop()->shouldBeCalled()->willReturn($span);
        $httpFoundationSpanFactory->fromOutgoingResponse($response, $span)->shouldBeCalled()->willReturn($span);
        $tracer->trace([$span])->shouldBeCalled();

        $this->onKernelTerminate($event);
    }

    function it_do_not_trace_if_no_request_in_stack(Tracer $tracer, SpanStack $spanStack, Span $span, PostResponseEvent $event)
    {
        $event->isMasterRequest()->willReturn(true);

        $spanStack->pop()->willReturn(null);
        $tracer->trace(Argument::any())->shouldNotBeCalled();

        $this->onKernelTerminate($event);
    }
}
