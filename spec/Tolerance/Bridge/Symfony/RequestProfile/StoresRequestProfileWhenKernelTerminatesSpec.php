<?php

namespace spec\Tolerance\Bridge\Symfony\RequestProfile;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\HttpFoundationProfileFactory;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Storage\ProfileStorage;


class StoresRequestProfileWhenKernelTerminatesSpec extends ObjectBehavior
{
    function let(HttpFoundationProfileFactory $httpFoundationProfileFactory, ProfileStorage $profileStorage)
    {
        $this->beConstructedWith($httpFoundationProfileFactory, $profileStorage);
    }

    function it_stores_the_request_profile_when_the_kernel_terminates(PostResponseEvent $event, HttpFoundationProfileFactory $httpFoundationProfileFactory, ProfileStorage $profileStorage, MessageProfile $messageProfile)
    {
        $request = Request::create('/');
        $response = Response::create(null, 200);

        $event->getResponse()->willReturn($response);
        $event->getRequest()->willReturn($request);

        $httpFoundationProfileFactory->fromRequestAndResponse($request, $response)->willReturn($messageProfile);
        $profileStorage->store($messageProfile)->shouldBeCalled();

        $this->onKernelTerminate($event);
    }
}
