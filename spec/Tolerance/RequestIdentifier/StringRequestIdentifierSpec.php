<?php

namespace spec\Tolerance\RequestIdentifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\RequestIdentifier\RequestIdentifier;

class StringRequestIdentifierSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedFromString('foo');
    }

    function it_is_a_request_identifier()
    {
        $this->shouldImplement(RequestIdentifier::class);
    }

    function it_returns_the_string()
    {
        $this->get()->shouldReturn('foo');
    }
}
