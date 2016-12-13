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
     * @param int    $interval
     *
     * @return array
     */
    public function getQueue($vhost, $name, $interval = 30)
    {
        $queueName = sprintf('%s/%s', urlencode($vhost), urlencode($name));
        $url = sprintf('http://%s:%d/api/queues/%s?%s', $this->hostname, $this->port, $queueName, http_build_query([
            'lengths_age' => $interval,
            'lengths_incr' => $interval,
            'msg_rates_age' => $interval,
            'msg_rates_incr' => $interval,
            'data_rates_age' => $interval,
            'data_rates_incr' => $interval,
        ]));


        $options = [
            'auth' => [
                $this->user,
                $this->password,
            ],
        ];

        if (version_compare(ClientInterface::VERSION, '6.0') >= 0) {
            $response = $this->httpClient->request('get', $url, $options);
        } else {
            $response = $this->httpClient->get($url, $options);
        }

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }
}
