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
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\HttpRequest\HttpMessageProfile;
use Tolerance\MessageProfile\Peer\MessagePeer;

final class SimpleHttpFoundationProfileFactory implements HttpFoundationProfileFactory
{
    /**
     * @var RequestIdentifierResolver
     */
    private $requestIdentifierResolver;

    /**
     * @param RequestIdentifierResolver $requestIdentifierResolver
     */
    public function __construct(RequestIdentifierResolver $requestIdentifierResolver)
    {
        $this->requestIdentifierResolver = $requestIdentifierResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function fromRequestAndResponse(Request $request, Response $response = null, MessagePeer $sender = null, MessagePeer $recipient = null)
    {
        return new HttpMessageProfile(
            $this->requestIdentifierResolver->resolve($request),
            $sender,
            $recipient,
            [],
            null,
            $request->getMethod(),
            $request->getUri(),
            null !== $response ? $response->getStatusCode() : 0
        );
    }
}
