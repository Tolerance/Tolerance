<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Storage;

use Tolerance\MessageProfile\MessageProfile;

interface ProfileStorage
{
    /**
     * Store the given message profile.
     *
     * @param MessageProfile $profile
     */
    public function store(MessageProfile $profile);
}
