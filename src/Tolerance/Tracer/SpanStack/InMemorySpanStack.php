<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\SpanStack;

use Tolerance\Tracer\Span\Span;

final class InMemorySpanStack implements SpanStack
{
    /**
     * @var Span[]
     */
    private $stack = [];

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (false !== ($span = end($this->stack))) {
            return $span;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function push(Span $span)
    {
        $this->stack[] = $span;

        return $span;
    }

    /**
     * {@inheritdoc}
     */
    public function pop()
    {
        return array_pop($this->stack);
    }
}
