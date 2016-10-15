<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Timing;

class SimpleMessageTiming implements MessageTiming
{
    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * Duration, in seconds.
     *
     * @var float
     */
    private $duration;

    private function __construct()
    {
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return SimpleMessageTiming
     */
    public static function fromRange(\DateTime $start, \DateTime $end)
    {
        $timing = new self();
        $timing->start = $start;
        $timing->end = $end;
        $timing->duration = ((float) $end->format('U.u') - (float) $start->format('U.u')) * 1000;

        return $timing;
    }

    /**
     * {@inheritdoc}
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * {@inheritdoc}
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
