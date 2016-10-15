<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Metrics\Collector\RabbitMq;

use GuzzleHttp\ClientInterface;

class RabbitMqHttpClient
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @param ClientInterface $httpClient
     * @param string          $hostname
     * @param int             $port
     * @param string          $user
     * @param string          $password
     */
    public function __construct(ClientInterface $httpClient, $hostname, $port, $user, $password)
    {
        $this->httpClient = $httpClient;
        $this->hostname = $hostname;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param string $vhost
     * @param string $name
     *
     * @return array
     */
    public function getQueue($vhost, $name)
    {
        $queueName = sprintf('%s/%s', urlencode($vhost), urlencode($name));
        $url = sprintf('http://%s:%d/api/queues/%s', $this->hostname, $this->port, $queueName);

        $response = $this->httpClient->get($url, [
            'auth' => [
                $this->user,
                $this->password,
            ],
        ]);

        return $response->json();
    }
}
