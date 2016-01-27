<?php

namespace spec\Tolerance\MessageProfile\Identifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;

class StringMessageIdentifierSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedFromString('foo');
    }

    function it_is_a_message_identifier()
    {
        $this->shouldImplement(MessageIdentifier::class);
    }

    function it_can_be_converted_to_string()
    {
        $this->__toString()->shouldReturn('foo');
    }
}
