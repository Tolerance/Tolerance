<?php

namespace spec\Tolerance\Bridge\Symfony\MessageProfile;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tolerance\MessageProfile\Context\InMemoryMessageContext;
use Tolerance\MessageProfile\Context\MessageContext;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;

class PopulateMessageContextFromRequestSpec extends ObjectBehavior
{
    function let(RequestIdentifierResolver $requestIdentifierResolver, MessageContext $messageContext)
    {
        $this->beConstructedWith($requestIdentifierResolver, $messageContext);
    }

    function it_set_the_message_identifier_in_the_context(GetResponseEvent $event, RequestIdentifierResolver $requestIdentifierResolver, MessageContext $messageContext, MessageIdentifier $identifier)
    {
        $event->isMasterRequest()->willReturn(true);
        $request = Request::create('');
        $event->getRequest()->willReturn($request);

        $requestIdentifierResolver->resolve($request)->shouldBeCalled()->willReturn($identifier);
        $messageContext->setIdentifier($identifier)->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
