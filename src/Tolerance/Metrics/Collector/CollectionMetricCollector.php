<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Metrics\Collector;

final class CollectionMetricCollector implements MetricCollector
{
    /**
     * @var array|MetricCollector[]
     */
    private $collectors;

    /**
     * @param MetricCollector[] $collectors
     */
    public function __construct(array $collectors = [])
    {
        $this->collectors = $collectors;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return array_reduce($this->collectors, function (array $metrics, MetricCollector $collector) {
            return array_merge($metrics, $collector->collect());
        }, []);
    }

    /**
     * @param MetricCollector $collector
     */
    public function addCollector(MetricCollector $collector)
    {
        $this->collectors[] = $collector;
    }
}
