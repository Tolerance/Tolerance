<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Metrics\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestMetricNamespaceResolver
{
    /**
     * Return the namespace that needs to be used for the given request.
     *
     * @param Request $request
     *
     * @return string
     */
    public function resolve(Request $request);
}
