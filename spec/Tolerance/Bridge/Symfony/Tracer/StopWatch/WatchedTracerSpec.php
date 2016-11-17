<?php

namespace spec\Tolerance\Bridge\Symfony\Tracer\StopWatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Stopwatch\Stopwatch;
use Tolerance\Tracer\Span\Identifier;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\Tracer;

class WatchedTracerSpec extends ObjectBehavior
{
    function let(Tracer $decoratedTracer, Stopwatch $stopwatch)
    {
        $this->beConstructedWith($decoratedTracer, $stopwatch);
    }

    function it_is_a_tracer()
    {
        $this->shouldImplement(Tracer::class);
    }

    function it_starts_and_stop_before_an_after_delegating_the_trace(Tracer $decoratedTracer, Stopwatch $stopwatch)
    {
        $stopwatch->start(Argument::type('string'))->shouldBeCalled();
        $decoratedTracer->trace([])->shouldBeCalled();
        $stopwatch->stop(Argument::type('string'))->shouldBeCalled();

        $this->trace([]);
    }

    function it_uses_the_span_name_when_only_one_is_traced(Tracer $decoratedTracer, Stopwatch $stopwatch)
    {
        $spans = [new Span(Identifier::fromString('1234'), 'name', Identifier::fromString('1234'))];

        $stopwatch->start('trace (name)')->shouldBeCalled();
        $stopwatch->stop('trace (name)')->shouldBeCalled();

        $this->trace($spans);
    }
}
