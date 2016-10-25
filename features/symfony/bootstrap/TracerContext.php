<?php

use Behat\Behat\Context\Context;
use Tolerance\Tracer\InMemoryTracer;

class TracerContext implements Context
{
    /**
     * @var InMemoryTracer
     */
    private $inMemoryTracer;

    /**
     * @param InMemoryTracer $inMemoryTracer
     */
    public function __construct(InMemoryTracer $inMemoryTracer)
    {
        $this->inMemoryTracer = $inMemoryTracer;
    }

    /**
     * @Then at least :count span should have been stored
     */
    public function atLeastSpanShouldHaveBeenStored($count)
    {
        $spans = $this->inMemoryTracer->getSpans();

        if (count($spans) < $count) {
            throw new \RuntimeException(sprintf('Found %d spans instead', count($spans)));
        }
    }
}
