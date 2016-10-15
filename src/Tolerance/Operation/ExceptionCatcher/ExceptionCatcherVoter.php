<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\ExceptionCatcher;

/**
 * @deprecated Use ThrowableCatcherVoter instead
 */
interface ExceptionCatcherVoter
{
    /**
     * Decides if whatever we should catch the given exception.
     *
     * @param \Exception $e
     *
     * @return bool
     *
     * @deprecated
     */
    public function shouldCatch(\Exception $e);
}
