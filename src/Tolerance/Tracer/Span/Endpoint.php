<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\Span;

class Endpoint
{
    /**
     * @var string
     */
    private $ipv4;

    /**
     * @var string
     */
    private $ipv6;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @param string $ipv4
     * @param string $ipv6
     * @param int    $port
     * @param string $serviceName
     */
    public function __construct($ipv4, $ipv6, $port, $serviceName)
    {
        $this->ipv4 = $ipv4;
        $this->ipv6 = $ipv6;
        $this->port = $port;
        $this->serviceName = $serviceName;
    }

    /**
     * @return string
     */
    public function getIpv4()
    {
        return $this->ipv4;
    }

    /**
     * @return string
     */
    public function getIpv6()
    {
        return $this->ipv6;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
}
