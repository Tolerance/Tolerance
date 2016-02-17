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
use Tolerance\MessageProfile\Peer\MessagePeer;

interface HttpFoundationProfileFactory
{
    /**
     * @param Request     $request
     * @param Response    $response
     * @param MessagePeer $sender
     * @param MessagePeer $recipient
     *
     * @return HttpMessageProfile
     */
    public function fromRequestAndResponse(Request $request, Response $response = null, MessagePeer $sender = null, MessagePeer $recipient = null);
}
