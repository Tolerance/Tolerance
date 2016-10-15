<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\Rate;

/**
 * Defines a rate in time.
 *
 * @see https://github.com/bandwidth-throttle/token-bucket/blob/master/classes/Rate.php
 */
class TimeRate implements Rate
{
    const PER_MICROSECOND = 'microsecond';
    const PER_MILLISECOND = 'millisecond';
    const PER_SECOND = 'second';
    const PER_MINUTE = 'minute';
    const PER_HOUR = 'hour';
    const PER_DAY = 'day';
    const PER_WEEK = 'week';
    const PER_MONTH = 'month';
    const PER_YEAR = 'year';

    /**
     * @var float[] Mapping between units and seconds
     */
    private static $unitMap = [
        self::PER_MICROSECOND => 0.000001,
        self::PER_MILLISECOND => 0.001,
        self::PER_SECOND => 1,
        self::PER_MINUTE => 60,
        self::PER_HOUR => 3600,
        self::PER_DAY => 86400,
        self::PER_WEEK => 604800,
        self::PER_MONTH => 2629743.83,
        self::PER_YEAR => 31556926,
    ];

    /**
     * @var int
     */
    private $ticks;

    /**
     * @var string
     */
    private $unit;

    /**
     * Sets the amount of ticks which will be produced per unit.
     *
     * E.g. new TimeRate(100, TimeRate::PER_SECOND) will produce 100 ticks per second.
     *
     * @param int    $ticks The amount of ticks per unit
     * @param string $unit  The unit as one of Rate's constants
     *
     * @throws \InvalidArgumentException The unit must be a valid constant
     */
    public function __construct($ticks, $unit)
    {
        if (!isset(self::$unitMap[$unit])) {
            throw new \InvalidArgumentException('Not a valid unit.');
        }
        $this->ticks = $ticks;
        $this->unit = $unit;
    }

    /**
     * It returns the number of ticks per second basically.
     *
     * @return float
     */
    public function getTicks()
    {
        return $this->ticks / self::$unitMap[$this->unit];
    }
}
