<?php

namespace spec\Tolerance\Bridge\Symfony\MessageProfile;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\HttpFoundationProfileFactory;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\MessagePeer;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;
use Tolerance\MessageProfile\Storage\ProfileStorage;
use Tolerance\MessageProfile\Timing\MessageTiming;


class StoresRequestProfileWhenKernelTerminatesSpec extends ObjectBehavior
{
    function let(HttpFoundationProfileFactory $httpFoundationProfileFactory, ProfileStorage $profileStorage, PeerResolver $peerResolver)
    {
        $this->beConstructedWith($httpFoundationProfileFactory, $profileStorage, $peerResolver);
    }

    function it_stores_the_request_profile_when_the_kernel_terminates(PostResponseEvent $event, HttpFoundationProfileFactory $httpFoundationProfileFactory, ProfileStorage $profileStorage, MessageProfile $messageProfile, PeerResolver $peerResolver, MessagePeer $messagePeer)
    {
        $messageProfile->withTiming(Argument::type(MessageTiming::class))->willReturn($messageProfile);

        $request = Request::create('/');
        $response = Response::create(null, 200);

        $event->getResponse()->willReturn($response);
        $event->getRequest()->willReturn($request);

        $peerResolver->resolve()->willReturn($messagePeer);

        $httpFoundationProfileFactory->fromRequestAndResponse($request, $response, null, $messagePeer)->willReturn($messageProfile);
        $profileStorage->store($messageProfile)->shouldBeCalled();

        $this->onKernelTerminate($event);
    }
}
