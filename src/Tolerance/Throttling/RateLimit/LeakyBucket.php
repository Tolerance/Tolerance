<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\RateLimit;

use Tolerance\Throttling\Rate\CounterRate;
use Tolerance\Throttling\Rate\Rate;
use Tolerance\Throttling\RateMeasureStorage\RateMeasureStorage;
use Tolerance\Throttling\RateMeasure\ImmutableRateMeasure;
use Tolerance\Throttling\RateMeasure\RateMeasure;

class LeakyBucket implements RateLimit
{
    /**
     * @var RateMeasureStorage
     */
    private $storage;

    /**
     * @var Rate
     */
    private $rate;

    /**
     * @param RateMeasureStorage $storage
     * @param Rate               $rate
     */
    public function __construct(RateMeasureStorage $storage, Rate $rate)
    {
        $this->storage = $storage;
        $this->rate = $rate;
    }

    /**
     * {@inheritdoc}
     */
    public function hasReachedLimit($identifier)
    {
        return $this->getTicksBeforeUnderLimit($identifier) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTicksBeforeUnderLimit($identifier)
    {
        $rate = $this->computeCurrentRate($identifier);

        $wait = $rate > 1000 ? $rate : 0;

        return $wait / 1000;
    }

    /**
     * {@inheritdoc}
     */
    public function tick($identifier)
    {
        $rate = $this->computeCurrentRate($identifier);

        $this->storage->save($identifier, new ImmutableRateMeasure(
            new CounterRate($rate),
            \DateTime::createFromFormat('U.u', microtime(true))
        ));
    }

    /**
     * @param string $identifier
     *
     * @return RateMeasure
     */
    private function getMeasure($identifier)
    {
        if (null === ($measure = $this->storage->find($identifier))) {
            $measure = new ImmutableRateMeasure(
                new CounterRate(0),
                \DateTime::createFromFormat('U', '0')
            );
        }

        return $measure;
    }

    /**
     * Create the current rate.
     *
     * @param string $identifier
     *
     * @return float
     */
    private function computeCurrentRate($identifier)
    {
        $measure = $this->getMeasure($identifier);
        $lastRequest = (float) $measure->getTime()->format('U.u');
        $lastRatio = $measure->getRate()->getTicks();

        $difference = (microtime(true) - $lastRequest) * 1000;

        $rate = max(0, $lastRatio - $difference);
        $rate += 1000 / $this->rate->getTicks();

        return $rate;
    }
}
