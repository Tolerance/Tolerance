<?php

namespace Tolerance\Tracer;

class InMemoryTracer implements Tracer
{
    private $spans = [];

    /**
     * {@inheritdoc}
     */
    public function trace(array $spans)
    {
        $this->spans[] = array_merge($this->spans, $spans);
    }

    /**
     * @return array
     */
    public function getSpans()
    {
        return $this->spans;
    }
}
