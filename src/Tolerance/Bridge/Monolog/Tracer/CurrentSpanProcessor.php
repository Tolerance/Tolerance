<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Monolog\Tracer;

use Tolerance\Tracer\SpanStack\SpanStack;

final class CurrentSpanProcessor
{
    /**
     * @var SpanStack
     */
    private $spanStack;

    /**
     * @param SpanStack $spanStack
     */
    public function __construct(SpanStack $spanStack)
    {
        $this->spanStack = $spanStack;
    }

    /**
     * Updates the record with the span identifiers.
     *
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        if (null === ($span = $this->spanStack->current())) {
            return $record;
        }

        $record['context']['tags'] = [
            'span_id' => (string) $span->getIdentifier(),
            'trace_id' => (string) $span->getTraceIdentifier(),
        ];

        return $record;
    }
}
