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
 * Voter on Throwable errors.
 *
 * This is extending ExceptionCatcherVoter for a BC layer. This extension should
 * be removed as soon as the ExceptionCatcherVoter is removed.
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
interface ThrowableCatcherVoter extends ExceptionCatcherVoter
{
    /**
     * Decides if whatever we should catch the given throwable.
     *
     * There is no typehint, because \Throwable isn't available before PHP 7.0,
     * so we need to handle \Exception too.
     *
     * @param \Exception|\Throwable $throwable
     *
     * @return bool
     */
    public function shouldCatchThrowable($throwable);
}
