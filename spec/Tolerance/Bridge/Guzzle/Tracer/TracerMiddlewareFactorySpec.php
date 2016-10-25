<?php

namespace spec\Tolerance\Bridge\Guzzle\Tracer;

use GuzzleHttp\Promise\PromiseInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use spec\Prophecy\Doubler\Generator\Node\ArgumentNodeSpec;
use Tolerance\Tracer\IdentifierGenerator\RandIdentifierGenerator;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanFactory\Psr7\Psr7SpanFactory;
use Tolerance\Tracer\Tracer;

class TracerMiddlewareFactorySpec extends ObjectBehavior
{
    function let(Psr7SpanFactory $psr7SpanFactory, Tracer $tracer, RequestInterface $request)
    {
        $request->withHeader(Argument::any(), Argument::any())->willReturn($request);

        $this->beConstructedWith($psr7SpanFactory, $tracer);
    }

    function it_creates_a_middle_ware()
    {
        $this->create()->shouldBeCallable();
    }

    function it_traces_the_outgoing_request(Psr7SpanFactory $psr7SpanFactory, Tracer $tracer, RequestInterface $request, PromiseInterface $promise)
    {
        $span = $this->generateSpan();
        $psr7SpanFactory->fromOutgoingRequest($request)->shouldBeCalled()->willReturn($span);
        $tracer->trace([$span])->shouldBeCalled();

        $middlewareFactory = $this->create();
        $middleware = $middlewareFactory(function() use ($promise) {
            return $promise;
        });
        $middleware($request, []);
    }

    function it_add_the_tracing_http_headers_the_outgoing_request(Psr7SpanFactory $psr7SpanFactory, Tracer $tracer, RequestInterface $request, PromiseInterface $promise)
    {
        $span = $this->generateSpan();
        $psr7SpanFactory->fromOutgoingRequest($request)->willReturn($span);

        $request->withHeader('X-B3-SpanId', (string) $span->getIdentifier())->shouldBeCalled()->willReturn($request);
        $request->withHeader('X-B3-TraceId', (string) $span->getTraceIdentifier())->shouldBeCalled()->willReturn($request);
        $request->withHeader('X-B3-ParentSpanId', (string) $span->getParentIdentifier())->shouldBeCalled()->willReturn($request);

        $middlewareFactory = $this->create();
        $middleware = $middlewareFactory(function(RequestInterface $request) use ($promise, $span) {

            return $promise;
        });

        $middleware($request, []);
    }

    /**
     * @return Span
     */
    private function generateSpan()
    {
        $identifier = (new RandIdentifierGenerator())->generate();

        return new Span($identifier, 'Name', $identifier);
    }
}
