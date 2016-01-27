<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\HttpRequest\HttpFoundation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tolerance\MessageProfile\HttpRequest\HttpMessageProfile;

interface HttpFoundationProfileFactory
{
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return HttpMessageProfile
     */
    public function fromRequestAndResponse(Request $request, Response $response);
}
