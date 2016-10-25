<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\Runner\Metrics;

use Tolerance\Metrics\Metric;
use Tolerance\Metrics\Publisher\MetricPublisher;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Runner\OperationRunner;

final class SuccessFailurePublisherOperationRunner implements OperationRunner
{
    /**
     * @var OperationRunner
     */
    private $decoratedOperationRunner;

    /**
     * @var MetricPublisher
     */
    private $metricPublisher;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param OperationRunner $decoratedOperationRunner
     * @param MetricPublisher $metricPublisher
     * @param string          $namespace
     */
    public function __construct(OperationRunner $decoratedOperationRunner, MetricPublisher $metricPublisher, $namespace)
    {
        $this->decoratedOperationRunner = $decoratedOperationRunner;
        $this->metricPublisher = $metricPublisher;
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        try {
            $result = $this->decoratedOperationRunner->run($operation);

            $this->metricPublisher->publish([
                new Metric($this->namespace.'.success', 1, Metric::TYPE_INCREMENT),
            ]);

            return $result;
        } catch (\Exception $e) {
            // Will be re-thrown later
        } catch (\Throwable $e) {
            // Will be re-thrown later
        }

        $this->metricPublisher->publish([
            new Metric($this->namespace.'.failure', 1, Metric::TYPE_INCREMENT),
        ]);

        throw $e;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $this->decoratedOperationRunner->supports($operation);
    }
}
