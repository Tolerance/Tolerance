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

class Annotation
{
    const CLIENT_SEND = 'cs';
    const CLIENT_RECEIVE = 'cr';
    const SERVER_SEND = 'ss';
    const SERVER_RECEIVE = 'sr';
    const CLIENT_ADDRESS = 'ca';
    const SERVER_ADDRESS = 'sa';
    const LOCAL_COMPONENT = 'lc';

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var Endpoint|null
     */
    private $host;

    /**
     * @param string        $value
     * @param int           $timestamp
     * @param Endpoint|null $host
     */
    public function __construct($value, $timestamp, Endpoint $host = null)
    {
        $this->value = $value;
        $this->timestamp = $timestamp;
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return null|Endpoint
     */
    public function getHost()
    {
        return $this->host;
    }
}
