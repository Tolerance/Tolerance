<?php

namespace spec\Tolerance\Bridge\Guzzle\MessageProfile;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tolerance\MessageProfile\HttpRequest\Psr7\Psr7ProfileFactory;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\MessagePeer;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;
use Tolerance\MessageProfile\Storage\ProfileStorage;

class StoresRequestProfileMiddlewareFactorySpec extends ObjectBehavior
{
    function let(ProfileStorage $profileStorage, Psr7ProfileFactory $profileFactory, PeerResolver $peerResolver)
    {
        $this->beConstructedWith($profileStorage, $profileFactory, $peerResolver);
    }

    function it_returns_a_middleware()
    {
        $this->create()->shouldBeCallable();
    }

    // We cannot really test it because of the promises... :(
}
