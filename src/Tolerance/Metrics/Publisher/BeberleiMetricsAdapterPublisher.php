<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Metrics\Publisher;

use Beberlei\Metrics\Collector\Collector;
use Tolerance\Metrics\Metric;

final class BeberleiMetricsAdapterPublisher implements MetricPublisher
{
    /**
     * @var Collector
     */
    private $beberleiCollector;

    /**
     * @var bool
     */
    private $autoFlush;

    /**
     * @param Collector $beberleiCollector
     * @param bool      $autoFlush
     */
    public function __construct(Collector $beberleiCollector, $autoFlush = true)
    {
        $this->beberleiCollector = $beberleiCollector;
        $this->autoFlush = $autoFlush;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(array $metrics)
    {
        foreach ($metrics as $metric) {
            $this->publishMetric($metric);
        }

        if ($this->autoFlush) {
            $this->beberleiCollector->flush();
        }
    }

    /**
     * @param Metric $metric
     */
    private function publishMetric(Metric $metric)
    {
        if ($metric->getType() == Metric::TYPE_INCREMENT) {
            $this->beberleiCollector->increment($metric->getName());
        } elseif ($metric->getType() == Metric::TYPE_DECREMENT) {
            $this->beberleiCollector->decrement($metric->getName());
        } elseif ($metric->getType() == Metric::TYPE_TIMING) {
            $this->beberleiCollector->timing($metric->getName(), $metric->getValue());
        } else {
            $this->beberleiCollector->measure($metric->getName(), $metric->getValue());
        }
    }
}
