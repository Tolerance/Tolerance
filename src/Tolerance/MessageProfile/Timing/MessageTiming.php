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

interface MessageTiming
{
    /**
     * Beginning of the request.
     *
     * @return \DateTime
     */
    public function getStart();

    /**
     * Beginning of the request.
     *
     * @return \DateTime
     */
    public function getEnd();
}
