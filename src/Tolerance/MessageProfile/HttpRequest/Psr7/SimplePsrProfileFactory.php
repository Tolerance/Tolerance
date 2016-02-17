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
use Tolerance\MessageProfile\HttpRequest\HttpMessageProfile;
use Tolerance\MessageProfile\HttpRequest\Psr7\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\Peer\MessagePeer;

class SimplePsrProfileFactory implements Psr7ProfileFactory
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
    public function fromRequestAndResponse(RequestInterface $request, ResponseInterface $response = null, MessagePeer $sender = null, MessagePeer $recipient = null)
    {
        return new HttpMessageProfile(
            $this->requestIdentifierResolver->resolve($request),
            $sender,
            $recipient,
            [],
            null,
            $request->getMethod(),
            (string) $request->getUri(),
            null !== $response ? $response->getStatusCode() : 0
        );
    }
}
