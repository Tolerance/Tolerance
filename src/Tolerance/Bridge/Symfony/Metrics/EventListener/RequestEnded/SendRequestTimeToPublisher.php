<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Metrics\EventListener\RequestEnded;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tolerance\Bridge\Symfony\Events;
use Tolerance\Bridge\Symfony\Metrics\Event\RequestEnded;
use Tolerance\Bridge\Symfony\Metrics\Request\RequestMetricNamespaceResolver;
use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;

final class SendRequestTimeToPublisher implements EventSubscriberInterface
{
    /**
     * @var MetricPublisher
     */
    private $metricPublisher;

    /**
     * @var RequestMetricNamespaceResolver
     */
    private $requestMetricNamespaceResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param MetricPublisher                $metricPublisher
     * @param RequestMetricNamespaceResolver $requestMetricNamespaceResolver
     * @param LoggerInterface                $logger
     */
    public function __construct(MetricPublisher $metricPublisher, RequestMetricNamespaceResolver $requestMetricNamespaceResolver, LoggerInterface $logger = null)
    {
        $this->metricPublisher = $metricPublisher;
        $this->requestMetricNamespaceResolver = $requestMetricNamespaceResolver;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::REQUEST_ENDS => ['onRequestEnd'],
        ];
    }

    /**
     * @param RequestEnded $event
     */
    public function onRequestEnd(RequestEnded $event)
    {
        $request = $event->getRequest();

        if (null === ($requestTime = $request->attributes->get('_tolerance_request_time', null))) {
            $this->logger !== null && $this->logger->debug('The request do not contain the start time');

            return;
        }

        $requestDurationInSeconds = microtime(true) - ((float) $requestTime);
        $requestDurationInMilliseconds = $requestDurationInSeconds * 1000;

        $namespace = $this->requestMetricNamespaceResolver->resolve($request);

        $this->metricPublisher->publish([
            new Metric(
                $namespace,
                $requestDurationInMilliseconds,
                Metric::TYPE_TIMING
            ),
            new Metric(
                $namespace,
                null,
                Metric::TYPE_INCREMENT
            ),
        ]);
    }
}
