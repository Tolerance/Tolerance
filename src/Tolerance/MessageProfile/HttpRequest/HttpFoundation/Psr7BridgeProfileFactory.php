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

use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tolerance\MessageProfile\HttpRequest\Psr7\Psr7ProfileFactory;
use Tolerance\MessageProfile\Peer\MessagePeer;

final class Psr7BridgeProfileFactory implements HttpFoundationProfileFactory
{
    /**
     * @var Psr7ProfileFactory
     */
    private $profileFactory;

    /**
     * @param Psr7ProfileFactory $profileFactory
     */
    public function __construct(Psr7ProfileFactory $profileFactory)
    {
        $this->profileFactory = $profileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function fromRequestAndResponse(Request $request, Response $response = null, MessagePeer $sender = null, MessagePeer $recipient = null)
    {
        $psr7Factory = new DiactorosFactory();

        return $this->profileFactory->fromRequestAndResponse(
            $psr7Factory->createRequest($request),
            null !== $response ? $psr7Factory->createResponse($response) : null,
            $sender,
            $recipient
        );
    }
}
