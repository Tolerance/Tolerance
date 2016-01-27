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
     * @var float
     */
    private $milliseconds;

    private function __construct()
    {
    }

    /**
     * @param float $milliseconds
     *
     * @return SimpleMessageTiming
     */
    public static function fromMilliseconds($milliseconds)
    {
        $timing = new self();
        $timing->milliseconds = $milliseconds;

        return $timing;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->milliseconds;
    }
}
