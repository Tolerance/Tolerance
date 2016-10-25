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

class BinaryAnnotation
{
    const TYPE_BOOLEAN = 0;
    const TYPE_BYTES = 1;
    const TYPE_INTEGER_16 = 2;
    const TYPE_INTEGER_32 = 3;
    const TYPE_INTEGER_64 = 4;
    const TYPE_DOUBLE = 5;
    const TYPE_STRING = 6;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $type;

    /**
     * @var Endpoint|null
     */
    private $host;

    /**
     * @param string        $key
     * @param string        $value
     * @param int           $type
     * @param Endpoint|null $host
     */
    public function __construct($key, $value, $type, Endpoint $host = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return null|Endpoint
     */
    public function getHost()
    {
        return $this->host;
    }
}
