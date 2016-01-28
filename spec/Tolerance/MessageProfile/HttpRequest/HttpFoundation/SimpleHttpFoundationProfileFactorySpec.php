<?php

namespace spec\Tolerance\MessageProfile\HttpRequest\HttpFoundation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\HttpFoundationProfileFactory;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\HttpRequest\HttpMessageProfile;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;

class SimpleHttpFoundationProfileFactorySpec extends ObjectBehavior
{
    function let(RequestIdentifierResolver $requestIdentifierResolver, PeerResolver $peerResolver)
    {
        $this->beConstructedWith($requestIdentifierResolver, $peerResolver);
    }

    function it_is_an_http_foundation_profile_factory()
    {
        $this->shouldImplement(HttpFoundationProfileFactory::class);
    }

    function it_generates_an_http_message_profile(RequestIdentifierResolver $requestIdentifierResolver, MessageIdentifier $messageIdentifier)
    {
        $request = Request::create('/');
        $response = new Response();

        $requestIdentifierResolver->resolve($request)->willReturn($messageIdentifier);

        $this->fromRequestAndResponse($request, $response)->shouldReturnAnInstanceOf(HttpMessageProfile::class);
    }
}
