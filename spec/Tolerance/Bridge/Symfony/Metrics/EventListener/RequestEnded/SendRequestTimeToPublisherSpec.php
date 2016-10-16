<?php

namespace spec\Tolerance\Bridge\Symfony\Metrics\EventListener\RequestEnded;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Tolerance\Bridge\Symfony\Metrics\Event\RequestEnded;
use Tolerance\Bridge\Symfony\Metrics\Request\RequestMetricNamespaceResolver;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;

class SendRequestTimeToPublisherSpec extends ObjectBehavior
{
    function let(MetricPublisher $metricPublisher, RequestMetricNamespaceResolver $requestMetricNamespaceResolver, LoggerInterface $logger)
    {
        $this->beConstructedWith($metricPublisher, $requestMetricNamespaceResolver, $logger);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_publishes_the_timing_and_increment_metrics(MetricPublisher $metricPublisher, RequestMetricNamespaceResolver $requestMetricNamespaceResolver)
    {
        $request = new Request([], [], ['_tolerance_request_time' => 123456.789]);

        $requestMetricNamespaceResolver->resolve($request)->willReturn('the_namespace');
        $metricPublisher->publish(Argument::that(function($metrics) {
            if (!is_array($metrics)) {
                return false;
            } elseif (count($metrics) != 2) {
                return false;
            } elseif ($metrics[0]->getName() != 'the_namespace') {
                return false;
            }

            return true;
        }))->shouldBeCalled();

        $this->onRequestEnd(new RequestEnded($request));
    }
}
