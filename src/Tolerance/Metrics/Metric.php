<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Metrics;

class Metric
{
    const TYPE_MEASURE = 1;
    const TYPE_INCREMENT = 2;
    const TYPE_DECREMENT = 4;
    const TYPE_TIMING = 8;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $value;

    /**
     * @var int
     */
    private $type;

    /**
     * @param string $name
     * @param int    $value
     * @param int    $type
     */
    public function __construct($name, $value, $type = self::TYPE_MEASURE)
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
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
}
