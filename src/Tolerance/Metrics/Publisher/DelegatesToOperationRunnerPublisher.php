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

use Tolerance\Operation\Callback;
use Tolerance\Operation\Runner\OperationRunner;

class DelegatesToOperationRunnerPublisher implements MetricPublisher
{
    /**
     * @var MetricPublisher
     */
    private $decoratedPublisher;

    /**
     * @var OperationRunner
     */
    private $operationRunner;

    /**
     * @param MetricPublisher $decoratedPublisher
     * @param OperationRunner $operationRunner
     */
    public function __construct(MetricPublisher $decoratedPublisher, OperationRunner $operationRunner)
    {
        $this->decoratedPublisher = $decoratedPublisher;
        $this->operationRunner = $operationRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(array $metrics)
    {
        return $this->operationRunner->run(new Callback(function() use ($metrics) {
            return $this->decoratedPublisher->publish($metrics);
        }));
    }
}
