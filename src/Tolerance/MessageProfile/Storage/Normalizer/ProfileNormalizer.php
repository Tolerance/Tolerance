<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Storage\Normalizer;

use Tolerance\MessageProfile\MessageProfile;

interface ProfileNormalizer
{
    /**
     * Normalize a message profile object into an interchangeable raw array.
     *
     * @param MessageProfile $profile
     *
     * @return array
     */
    public function normalize(MessageProfile $profile);
}
