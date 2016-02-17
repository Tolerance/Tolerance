<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\MessageProfile;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tolerance\MessageProfile\Context\MessageContext;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;

final class PopulateMessageContextFromRequest
{
    /**
     * @var MessageContext
     */
    private $messageContext;

    /**
     * @var RequestIdentifierResolver
     */
    private $requestIdentifierResolver;

    /**
     * @param RequestIdentifierResolver $requestIdentifierResolver
     * @param MessageContext            $messageContext
     */
    public function __construct(RequestIdentifierResolver $requestIdentifierResolver, MessageContext $messageContext)
    {
        $this->requestIdentifierResolver = $requestIdentifierResolver;
        $this->messageContext = $messageContext;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $identifier = $this->requestIdentifierResolver->resolve($event->getRequest());
        $this->messageContext->setIdentifier($identifier);
    }
}
