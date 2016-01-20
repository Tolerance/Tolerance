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

interface RateMeasure
{
    /**
     * @return Rate
     */
    public function getRate();

    /**
     * @return \DateTime
     */
    public function getTime();
}
