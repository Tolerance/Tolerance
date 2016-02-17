<?php

namespace spec\Tolerance\MessageProfile\HttpRequest\Psr7\ProfileEnhancer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tolerance\MessageProfile\Context\MessageContext;
use Tolerance\MessageProfile\HttpRequest\Psr7\Psr7ProfileFactory;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\MessagePeer;

class ParentMessageEnhancerSpec extends ObjectBehavior
{
    function let(Psr7ProfileFactory $profileFactory, MessageContext $messageContext)
    {
        $this->beConstructedWith($profileFactory, $messageContext);
    }

    function it_is_a_psr7_profile_factory()
    {
        $this->shouldImplement(Psr7ProfileFactory::class);
    }

    function it_call_the_decorated_factory(Psr7ProfileFactory $profileFactory, RequestInterface $request, ResponseInterface $response, MessagePeer $sender, MessagePeer $recipient, MessageProfile $profile)
    {
        $profileFactory->fromRequestAndResponse($request, $response, $sender, $recipient)->shouldBeCalled()->willReturn($profile);

        $this->fromRequestAndResponse($request, $response, $sender, $recipient)->shouldReturn($profile);
    }

    function it_adds_the_parent_identifier_to_the_profile_if_the_context_contains_an_identifier(Psr7ProfileFactory $profileFactory, MessageContext $messageContext, RequestInterface $request, MessageProfile $profile, MessageIdentifier $messageIdentifier)
    {
        $profileFactory->fromRequestAndResponse($request, null, null, null)->willReturn($profile);
        $messageContext->getIdentifier()->willReturn($messageIdentifier);
        $profile->withParentIdentifier($messageIdentifier)->shouldBeCalled()->willReturn($profile);

        $this->fromRequestAndResponse($request)->shouldReturn($profile);
    }
}
