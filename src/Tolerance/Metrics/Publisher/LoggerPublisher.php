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

use Psr\Log\LoggerInterface;

final class LoggerPublisher implements MetricPublisher
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(array $metrics)
    {
        foreach ($metrics as $metric) {
            $this->logger->info(sprintf(
                'Metric "%s": %s',
                $metric->getName(),
                $metric->getValue()
            ));
        }
    }
}
