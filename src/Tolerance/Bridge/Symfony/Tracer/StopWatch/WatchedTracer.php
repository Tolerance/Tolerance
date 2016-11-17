<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Tracer\StopWatch;

use Symfony\Component\Stopwatch\Stopwatch;
use Tolerance\Tracer\Tracer;

final class WatchedTracer implements Tracer
{
    /**
     * @var Tracer
     */
    private $decoratedTracer;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @param Tracer $decoratedTracer
     * @param Stopwatch $stopwatch
     */
    public function __construct(Tracer $decoratedTracer, Stopwatch $stopwatch)
    {
        $this->decoratedTracer = $decoratedTracer;
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function trace(array $spans)
    {
        $key = count($spans) == 1 ? $spans[0]->getName() : count($spans);
        $key = 'trace ('. $key . ')';

        $this->stopwatch->start($key);

        $this->decoratedTracer->trace($spans);

        $this->stopwatch->stop($key);
    }
}
