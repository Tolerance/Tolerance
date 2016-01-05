<?php

namespace spec\Tolerance\Bridge\Symfony\RequestIdentifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tolerance\RequestIdentifier\Storage\RequestIdentifierStorage;
use Tolerance\RequestIdentifier\StringRequestIdentifier;

class RequestHeadersListenerSpec extends ObjectBehavior
{
    function let(RequestIdentifierStorage $requestIdentifierStorage)
    {
        $this->beConstructedWith($requestIdentifierStorage, 'X-Request-Identifier');
    }

    function it_stores_the_requests_identifier_if_in_the_request_headers(HttpKernelInterface $kernel, RequestIdentifierStorage $requestIdentifierStorage)
    {
        $event = new GetResponseEvent(
            $kernel->getWrappedObject(),
            Request::create('/', 'GET', [], [], [], [
                'HTTP_X-Request-Identifier' => '12345'
            ]),
            HttpKernelInterface::MASTER_REQUEST
        );

        $requestIdentifierStorage->setRequestIdentifier(
            StringRequestIdentifier::fromString('12345')
        )->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
