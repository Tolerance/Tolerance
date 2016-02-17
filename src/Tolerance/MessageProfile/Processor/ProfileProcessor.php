<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Processor;

use Tolerance\MessageProfile\MessageProfile;

interface ProfileProcessor
{
    /**
     * @param MessageProfile $profile
     *
     * @return MessageProfile
     */
    public function process(MessageProfile $profile);
}
