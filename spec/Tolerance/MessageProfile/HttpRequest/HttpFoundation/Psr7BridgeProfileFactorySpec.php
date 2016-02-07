<?php

namespace spec\Tolerance\MessageProfile\HttpRequest\HttpFoundation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\HttpFoundationProfileFactory;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\HttpRequest\HttpMessageProfile;
use Tolerance\MessageProfile\HttpRequest\Psr7\Psr7ProfileFactory;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;

class Psr7BridgeProfileFactorySpec extends ObjectBehavior
{
    function let(Psr7ProfileFactory $psr7ProfileFactory)
    {
        $this->beConstructedWith($psr7ProfileFactory);
    }

    function it_is_an_http_foundation_profile_factory()
    {
        $this->shouldImplement(HttpFoundationProfileFactory::class);
    }

    function it_generates_an_http_message_profile(Psr7ProfileFactory $psr7ProfileFactory, MessageProfile $messageProfile)
    {
        $request = Request::create('/');
        $response = new Response();

        $psr7ProfileFactory->fromRequestAndResponse(
            Argument::type(RequestInterface::class),
            Argument::type(ResponseInterface::class),
            Argument::any(),
            Argument::any()
        )->willReturn($messageProfile);

        $this->fromRequestAndResponse($request, $response)->shouldReturn($messageProfile);
    }
}
