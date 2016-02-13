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
use Tolerance\MessageProfile\Context\MessageContext;
use Tolerance\MessageProfile\HttpRequest\Psr7\Psr7ProfileFactory;
use Tolerance\MessageProfile\Peer\MessagePeer;

final class ParentMessageEnhancer implements Psr7ProfileFactory
{
    /**
     * @var Psr7ProfileFactory
     */
    private $decoratedFactory;

    /**
     * @var MessageContext
     */
    private $messageContext;

    /**
     * @param Psr7ProfileFactory $decoratedFactory
     * @param MessageContext     $messageContext
     */
    public function __construct(Psr7ProfileFactory $decoratedFactory, MessageContext $messageContext)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->messageContext = $messageContext;
    }

    /**
     * {@inheritdoc}
     */
    public function fromRequestAndResponse(RequestInterface $request, ResponseInterface $response = null, MessagePeer $sender = null, MessagePeer $recipient = null)
    {
        $profile = $this->decoratedFactory->fromRequestAndResponse($request, $response, $sender, $recipient);

        if (null !== ($parentIdentifier = $this->messageContext->getIdentifier())) {
            $profile = $profile->withParentIdentifier($parentIdentifier);
        }

        return $profile;
    }
}
