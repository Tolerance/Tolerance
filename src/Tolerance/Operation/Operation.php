<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation;

interface Operation
{
    /**
     * Is the operation successful?
     *
     * @return bool
     */
    public function isSuccessful();

    /**
     * Is the operation failed?
     *
     * @return bool
     */
    public function isFailed();

    /**
     * Returns true if the operation was run.
     *
     * @return bool
     */
    public function hasRun();
}
