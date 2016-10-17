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

final class CollectionMetricPublisher implements MetricPublisher
{
    /**
     * @var array|MetricPublisher[]
     */
    private $publishers;

    /**
     * @param MetricPublisher[] $publishers
     */
    public function __construct(array $publishers = [])
    {
        $this->publishers = $publishers;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(array $metrics)
    {
        foreach ($this->publishers as $publisher) {
            $publisher->publish($metrics);
        }
    }

    /**
     * @param MetricPublisher $publisher
     */
    public function addPublisher(MetricPublisher $publisher)
    {
        $this->publishers[] = $publisher;
    }
}
