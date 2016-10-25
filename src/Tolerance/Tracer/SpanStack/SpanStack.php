<?php

namespace Tolerance\Tracer\SpanStack;

use Tolerance\Tracer\Span\Span;

interface SpanStack
{
    /**
     * Get the current span in the stack.
     *
     * @return Span|null
     */
    public function current();

    /**
     * Push a span in the stack.
     *
     * @param Span $span
     *
     * @return Span
     */
    public function push(Span $span);

    /**
     * Pop a span from the stack.
     *
     * @return Span|null
     */
    public function pop();
}
