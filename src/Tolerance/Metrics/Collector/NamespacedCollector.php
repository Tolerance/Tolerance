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

use Tolerance\Metrics\Metric;

final class NamespacedCollector implements MetricCollector
{
    /**
     * @var MetricCollector
     */
    private $decoratedCollector;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param MetricCollector $decoratedCollector
     * @param string          $namespace
     */
    public function __construct(MetricCollector $decoratedCollector, $namespace)
    {
        $this->decoratedCollector = $decoratedCollector;
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return array_map(function (Metric $metric) {
            return new Metric($this->namespace.'.'.$metric->getName(), $metric->getValue());
        }, $this->decoratedCollector->collect());
    }
}
