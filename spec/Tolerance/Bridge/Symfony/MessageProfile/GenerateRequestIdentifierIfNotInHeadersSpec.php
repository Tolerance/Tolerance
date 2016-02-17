<?php

namespace spec\Tolerance\Bridge\Symfony\MessageProfile;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;

class GenerateRequestIdentifierIfNotInHeadersSpec extends ObjectBehavior
{
    function let(MessageIdentifierGenerator $messageIdentifierGenerator, GetResponseEvent $event)
    {
        $this->beConstructedWith($messageIdentifierGenerator, 'x-message-id');

        $event->isMasterRequest()->willReturn(true);
    }

    function it_do_nothing_if_not_in_headers(GetResponseEvent $event, MessageIdentifierGenerator $messageIdentifierGenerator)
    {
        $event->getRequest()->willReturn(Request::create('', 'GET', [], [], [], ['HTTP_x-message-id' => '1234']));
        $messageIdentifierGenerator->generate()->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_generates_and_adds_the_identifier_if_not_in_headers(GetResponseEvent $event, HeaderBag $headerBag, MessageIdentifierGenerator $messageIdentifierGenerator)
    {
        $request = Request::create('');
        $request->headers = $headerBag->getWrappedObject();
        $event->getRequest()->willReturn($request);
        $event->isMasterRequest()->willReturn(true);

        $headerBag->has('x-message-id')->willReturn(false);
        $messageIdentifierGenerator->generate()->shouldBeCalled()->willReturn(StringMessageIdentifier::fromString('1234'));
        $headerBag->set('x-message-id', '1234')->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
