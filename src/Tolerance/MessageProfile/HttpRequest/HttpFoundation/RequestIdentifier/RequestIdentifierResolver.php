<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier;

use Symfony\Component\HttpFoundation\Request;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;

interface RequestIdentifierResolver
{
    /**
     * @param Request $request
     *
     * @return MessageIdentifier
     */
    public function resolve(Request $request);
}
