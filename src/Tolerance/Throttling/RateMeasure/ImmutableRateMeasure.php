<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\RateMeasure;

use Tolerance\Throttling\Rate\Rate;

final class ImmutableRateMeasure implements RateMeasure
{
    /**
     * @var Rate
     */
    private $rate;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @param Rate      $rate
     * @param \DateTime $time
     */
    public function __construct(Rate $rate, \DateTime $time)
    {
        $this->rate = $rate;
        $this->time = $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * {@inheritdoc}
     */
    public function getTime()
    {
        return $this->time;
    }
}
