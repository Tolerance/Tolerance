<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\RateMeasureStorage;

use Tolerance\Throttling\RateMeasure\RateMeasure;

interface RateMeasureStorage
{
    /**
     * Save a measure for the given identifier.
     *
     * @param string      $identifier
     * @param RateMeasure $measure
     */
    public function save($identifier, RateMeasure $measure);

    /**
     * Find a measure for the given identifier.
     *
     * @param string $identifier
     *
     * @return RateMeasure|null
     */
    public function find($identifier);
}
