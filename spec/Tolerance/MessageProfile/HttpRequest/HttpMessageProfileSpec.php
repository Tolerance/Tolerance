<?php

namespace spec\Tolerance\MessageProfile\HttpRequest;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\MessageProfile;

class HttpMessageProfileSpec extends ObjectBehavior
{
    function let(MessageIdentifier $messageIdentifier)
    {
        $this->beConstructedWith($messageIdentifier, null, null, [], null, 'GET', '/path', 200);
    }

    function it_is_a_message_profile()
    {
        $this->shouldImplement(MessageProfile::class);
    }

    function it_exposes_the_http_method()
    {
        $this->getMethod()->shouldReturn('GET');
    }

    function it_exposes_the_path()
    {
        $this->getPath()->shouldReturn('/path');
    }

    function it_exposes_the_status_code()
    {
        $this->getStatusCode()->shouldReturn(200);
    }
}
