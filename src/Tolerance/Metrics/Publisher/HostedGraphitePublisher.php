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

final class HostedGraphitePublisher implements MetricPublisher
{
    /**
     * @var string
     */
    private $server;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param string $server
     * @param int    $port
     * @param string $apiKey
     */
    public function __construct($server, $port, $apiKey)
    {
        $this->server = $server;
        $this->port = $port;
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(array $metrics)
    {
        $connection = fsockopen($this->server, $this->port);

        foreach ($metrics as $metric) {
            fwrite($connection, sprintf("%s.%s %s\n", $this->apiKey, $metric->getName(), $metric->getValue()));
        }

        fclose($connection);
    }
}
