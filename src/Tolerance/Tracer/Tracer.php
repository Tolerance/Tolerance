<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer;

use Tolerance\Tracer\Span\Span;

interface Tracer
{
    /**
     * @param Span[] $spans
     *
     * @throws TracerException
     */
    public function trace(array $spans);
}
