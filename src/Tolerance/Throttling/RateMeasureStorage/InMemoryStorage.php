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

class InMemoryStorage implements RateMeasureStorage
{
    private $measures = [];

    /**
     * {@inheritdoc}
     */
    public function save($identifier, RateMeasure $measure)
    {
        $this->measures[$identifier] = $measure;
    }

    /**
     * {@inheritdoc}
     */
    public function find($identifier)
    {
        if (!array_key_exists($identifier, $this->measures)) {
            return;
        }

        return $this->measures[$identifier];
    }
}
