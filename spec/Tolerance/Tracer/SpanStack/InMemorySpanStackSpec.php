<?php

namespace spec\Tolerance\Tracer\SpanStack;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Tracer\IdentifierGenerator\RandIdentifierGenerator;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\SpanStack\SpanStack;

class InMemorySpanStackSpec extends ObjectBehavior
{
    function it_is_a_span_stack()
    {
        $this->shouldImplement(SpanStack::class);
    }

    function it_allows_to_push_and_get_the_current_span()
    {
        $this->current()->shouldReturn(null);

        $firstSpan = $this->generateSpan();
        $this->push($firstSpan);
        $this->current()->shouldReturn($firstSpan);

        $secondSpan = $this->generateSpan();
        $this->push($secondSpan);
        $this->current()->shouldReturn($secondSpan);

        $this->pop()->shouldReturn($secondSpan);
        $this->current()->shouldReturn($firstSpan);

        $this->pop()->shouldReturn($firstSpan);
        $this->current()->shouldReturn(null);
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
