<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\HttpRequest\Psr7;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\MessagePeer;

interface Psr7ProfileFactory
{
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param MessagePeer       $sender
     * @param MessagePeer       $recipient
     *
     * @return MessageProfile
     */
    public function fromRequestAndResponse(RequestInterface $request, ResponseInterface $response = null, MessagePeer $sender = null, MessagePeer $recipient = null);
}
