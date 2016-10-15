<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Metrics\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tolerance\Metrics\Collector\MetricCollector;
use Tolerance\Metrics\Publisher\MetricPublisher;

class CollectAndPublishMetricsCommand extends Command
{
    private $collector;
    private $publisher;

    /**
     * @param MetricCollector $collector
     * @param MetricPublisher $publisher
     */
    public function __construct(MetricCollector $collector, MetricPublisher $publisher)
    {
        parent::__construct('tolerance:metrics:collect-and-publish');

        $this->collector = $collector;
        $this->publisher = $publisher;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $metrics = $this->collector->collect();
        $this->publisher->publish($metrics);

        if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln([
                sprintf('<info>Collected and published %d metrics</info>', count($metrics)),
            ]);
        }
    }
}
