<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\EndpointResolver;

use Tolerance\Tracer\Span\Endpoint;

class ServiceNameResolver implements EndpointResolver
{
    /**
     * @var string
     */
    private $serviceName;

    /**
     * @param string $serviceName
     */
    public function __construct($serviceName = null)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve()
    {
        $serverAddress = array_key_exists('SERVER_ADDR', $_SERVER) ? $_SERVER['SERVER_ADDR'] : '';

        return new Endpoint(
            $this->isIpv4($serverAddress) ? $serverAddress : null,
            $this->isIpv6($serverAddress) ? $serverAddress : null,
            null,
            $this->serviceName ?: $serverAddress
        );
    }

    /**
     * @param string $address
     *
     * @return bool
     */
    private function isIpv4($address)
    {
        return false !== filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * @param string $address
     *
     * @return bool
     */
    private function isIpv6($address)
    {
        return false !== filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }
}
