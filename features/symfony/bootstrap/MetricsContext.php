<?php

use Behat\Behat\Context\Context;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\TracedPublisher;

class MetricsContext implements Context
{
    /**
     * @var TracedPublisher
     */
    private $tracedPublisher;

    /**
     * @param TracedPublisher $tracedPublisher
     */
    public function __construct(TracedPublisher $tracedPublisher)
    {
        $this->tracedPublisher = $tracedPublisher;
    }

    /**
     * @Then the metric :metricName should have been published
     */
    public function theMetricShouldHaveBeenPublished($metricName)
    {
        $publishedMetrics = $this->tracedPublisher->getPublishedMetrics();
        $matchingMetrics = array_filter($publishedMetrics, function(Metric $metric) use ($metricName) {
            return $metric->getName() == $metricName;
        });

        if (count($matchingMetrics) == 0) {
            throw new \RuntimeException('The metric was not published');
        }
    }
}
