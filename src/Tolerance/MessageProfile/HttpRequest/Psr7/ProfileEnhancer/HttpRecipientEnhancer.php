<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\HttpRequest\Psr7\ProfileEnhancer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tolerance\MessageProfile\HttpRequest\Psr7\Psr7ProfileFactory;
use Tolerance\MessageProfile\Peer\ArbitraryPeer;
use Tolerance\MessageProfile\Peer\MessagePeer;

final class HttpRecipientEnhancer implements Psr7ProfileFactory
{
    /**
     * @var Psr7ProfileFactory
     */
    private $decoratedFactory;

    /**
     * @param Psr7ProfileFactory $decoratedFactory
     */
    public function __construct(Psr7ProfileFactory $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function fromRequestAndResponse(RequestInterface $request, ResponseInterface $response = null, MessagePeer $sender = null, MessagePeer $recipient = null)
    {
        $profile = $this->decoratedFactory->fromRequestAndResponse($request, $response, $sender, $recipient);

        if (null === $recipient) {
            $profile = $profile->withRecipient($this->guessRecipient($request, $response));
        }

        return $profile;
    }

    /**
     * @param RequestInterface       $request
     * @param ResponseInterface|null $response
     *
     * @return ArbitraryPeer
     */
    private function guessRecipient(RequestInterface $request, ResponseInterface $response = null)
    {
        return ArbitraryPeer::fromArray([
            'host' => $request->getUri()->getHost(),
            'virtual' => 1,
        ]);
    }
}
